<?php
/**
 * @version    3.5.2
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;

/**
 * Frontend helper for Accommodation Manager
 *
 * @since  3.2.0
 */
class Accommodation_managerHelper
{
	/**
	 * Build season-grouped table data from a list of periods.
	 *
	 * Returns an array of groups, each with 'heading' (string) and 'periods' (array).
	 * If split by season is disabled, returns a single group with empty heading.
	 *
	 * @param   array     $periods  Array of period objects with period_start
	 * @param   Registry  $params   Component params
	 *
	 * @return  array
	 *
	 * @since   3.2.0
	 */
	public static function buildSeasonGroups(array $periods, Registry $params): array
	{
		$splitBySeason = (int) $params->get('rates_split_by_season', 0);

		if (!$splitBySeason || empty($periods))
		{
			return [['heading' => '', 'periods' => $periods]];
		}

		$summerStartMonth = (int) $params->get('rates_summer_start_month', 5);
		$summerStartDay   = (int) $params->get('rates_summer_start_day', 1);
		$winterStartMonth = (int) $params->get('rates_winter_start_month', 11);
		$winterStartDay   = (int) $params->get('rates_winter_start_day', 1);

		$summerMMDD = $summerStartMonth * 100 + $summerStartDay;
		$winterMMDD = $winterStartMonth * 100 + $winterStartDay;

		$seasonGroups = [];

		foreach ($periods as $period)
		{
			$date       = new Date($period->period_start);
			$year       = (int) $date->format('Y');
			$month      = (int) $date->format('m');
			$day        = (int) $date->format('d');
			$periodMMDD = $month * 100 + $day;

			if ($periodMMDD >= $summerMMDD && $periodMMDD < $winterMMDD)
			{
				$key     = $year . '_1_summer';
				$heading = Text::_('COM_ACCOMMODATION_MANAGER_RATES_SUMMER') . ' ' . $year;
			}
			elseif ($periodMMDD >= $winterMMDD)
			{
				$key     = $year . '_2_winter';
				$heading = Text::_('COM_ACCOMMODATION_MANAGER_RATES_WINTER') . ' ' . $year . '/' . substr($year + 1, 2);
			}
			else
			{
				$key     = ($year - 1) . '_2_winter';
				$heading = Text::_('COM_ACCOMMODATION_MANAGER_RATES_WINTER') . ' ' . ($year - 1) . '/' . substr($year, 2);
			}

			if (!isset($seasonGroups[$key]))
			{
				$seasonGroups[$key] = ['heading' => $heading, 'periods' => []];
			}

			$seasonGroups[$key]['periods'][] = $period;
		}

		return array_values($seasonGroups);
	}

	/**
	 * Decode gallery JSON into an array of objects with localised alt text.
	 *
	 * @param   string|null  $galleryJson  Raw JSON from room_gallery column
	 * @param   string       $lang         Language suffix (de, it, en, fr, es)
	 *
	 * @return  array  Array of objects with image, image_mobile, alt
	 *
	 * @since   3.2.0
	 */
	public static function decodeGalleryItems(?string $galleryJson, string $lang): array
	{
		if (empty($galleryJson))
		{
			return [];
		}

		$decoded = json_decode($galleryJson, true);

		if (!is_array($decoded))
		{
			return [];
		}

		$items = [];

		foreach ($decoded as $galleryItem)
		{
			$items[] = (object) [
				'image'        => $galleryItem['image'] ?? '',
				'image_mobile' => $galleryItem['image_mobile'] ?? '',
				'alt'          => $galleryItem['alt_' . $lang] ?? '',
			];
		}

		return $items;
	}

	/**
	 * Build a base query for rooms with all localised columns and category JOIN.
	 *
	 * Models can chain additional WHERE / ORDER clauses on the returned query.
	 *
	 * @param   DatabaseInterface  $db  Database driver
	 *
	 * @return  \Joomla\Database\QueryInterface
	 *
	 * @since   3.2.0
	 */
	public static function buildRoomBaseQuery(DatabaseInterface $db)
	{
		$lang = self::getLanguageSuffix();

		return $db->getQuery(true)
			->select([
				$db->quoteName('a.id'),
				$db->quoteName('a.room_name'),
				$db->quoteName('a.room_code'),
				$db->quoteName('a.room_category'),
				$db->quoteName('a.room_surface'),
				$db->quoteName('a.room_people'),
				$db->quoteName('a.room_price_from'),
				$db->quoteName('a.room_class'),
				$db->quoteName('a.room_title_' . $lang, 'title'),
				$db->quoteName('a.room_intro_' . $lang, 'intro'),
				$db->quoteName('a.room_description_' . $lang, 'description'),
				$db->quoteName('a.room_thumbnail', 'thumbnail'),
				$db->quoteName('a.room_thumbnail_alt_' . $lang, 'thumbnail_alt'),
				$db->quoteName('a.room_floor_plan', 'floor_plan'),
				$db->quoteName('a.room_floor_plan_alt_' . $lang, 'floor_plan_alt'),
				$db->quoteName('a.room_gallery', 'gallery'),
				$db->quoteName('a.room_video', 'video'),
				$db->quoteName('a.ordering'),
				$db->quoteName('c.room_category_name_' . $lang, 'category_name'),
				$db->quoteName('c.room_category_description_' . $lang, 'category_description'),
			])
			->from($db->quoteName('#__accommodation_manager_rooms', 'a'))
			->join('LEFT', $db->quoteName('#__accommodation_manager_room_categories', 'c')
				. ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.room_category'))
			->where($db->quoteName('a.state') . ' = 1');
	}

	/**
	 * Load rates data (periods, typologies, grid) for a set of rooms.
	 *
	 * Returns null when rooms_show_rates is disabled or roomIds is empty.
	 *
	 * @param   Registry  $params   Component/menu params
	 * @param   array     $roomIds  Array of room IDs
	 *
	 * @return  array|null  Array with keys 'periods', 'typologies', 'ratesGrid' or null
	 *
	 * @since   3.2.0
	 */
	public static function loadRatesData(Registry $params, array $roomIds): ?array
	{
		if (!(int) $params->get('rooms_show_rates', 0) || empty($roomIds))
		{
			return null;
		}

		$ratesModel = Factory::getApplication()
			->bootComponent('com_accommodation_manager')
			->getMVCFactory()
			->createModel('Rates', 'Site');

		$hidePast = (bool) $params->get('rates_hide_past_periods', 0);

		return [
			'periods'    => $ratesModel->getPeriods($hidePast),
			'typologies' => $ratesModel->getTypologies(),
			'ratesGrid'  => $ratesModel->getRatesGrid($roomIds),
		];
	}

	/**
	 * Get the minimum rate for a room in the current period.
	 *
	 * @param   DatabaseInterface  $db      Database driver
	 * @param   int                $roomId  Room ID
	 *
	 * @return  string|null  Minimum rate as string, or null if no active period
	 *
	 * @since   3.7.0
	 */
	public static function getCurrentRate(DatabaseInterface $db, int $roomId): ?string
	{
		$query = $db->getQuery(true)
			->select('MIN(' . $db->quoteName('r.rate') . ')')
			->from($db->quoteName('#__accommodation_manager_rates', 'r'))
			->join('INNER', $db->quoteName('#__accommodation_manager_rate_periods', 'p')
				. ' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('r.period_id'))
			->where($db->quoteName('r.room_id') . ' = :roomId')
			->where($db->quoteName('r.state') . ' = 1')
			->where($db->quoteName('p.state') . ' = 1')
			->where('CURDATE() BETWEEN ' . $db->quoteName('p.period_start')
				. ' AND ' . $db->quoteName('p.period_end'))
			->bind(':roomId', $roomId, \Joomla\Database\ParameterType::INTEGER);

		$result = $db->setQuery($query)->loadResult();

		return $result ? (string) $result : null;
	}

	// ------------------------------------------------------------------
	// Public facade — use from articles, templates, plugins, scripts
	// ------------------------------------------------------------------

	/**
	 * Get all published rooms in the current language.
	 *
	 * @return  array  Array of room objects
	 *
	 * @since   3.6.0
	 */
	public static function getRooms(): array
	{
		return self::getMVCFactory()->createModel('Rooms', 'Site')->getItems();
	}

	/**
	 * Get all published categories in the current language.
	 *
	 * @return  array  Array of category objects
	 *
	 * @since   3.6.0
	 */
	public static function getCategories(): array
	{
		return self::getMVCFactory()->createModel('Categories', 'Site')->getItems();
	}

	/**
	 * Get a single published room by ID in the current language.
	 *
	 * @param   int  $id  Room ID
	 *
	 * @return  object|null  Room object or null if not found
	 *
	 * @since   3.6.0
	 */
	public static function getRoom(int $id): ?object
	{
		return self::getMVCFactory()->createModel('Room', 'Site')->getItem($id);
	}

	/**
	 * Get all published categories with their rooms nested as ->rooms property.
	 *
	 * Uses only 2 queries (categories + rooms), no N+1.
	 *
	 * @return  array  Array of category objects, each with a ->rooms array
	 *
	 * @since   3.6.0
	 */
	public static function getCategoriesWithRooms(): array
	{
		$categories = self::getCategories();
		$rooms      = self::getRooms();

		$roomsByCategory = [];

		foreach ($rooms as $room)
		{
			$catId = (int) $room->room_category;
			$roomsByCategory[$catId][] = $room;
		}

		foreach ($categories as $category)
		{
			$category->rooms = $roomsByCategory[(int) $category->id] ?? [];
		}

		return $categories;
	}

	/**
	 * Get the MVCFactory for the Accommodation Manager component.
	 *
	 * @return  \Joomla\CMS\MVC\Factory\MVCFactoryInterface
	 *
	 * @since   3.6.0
	 */
	private static function getMVCFactory()
	{
		return Factory::getApplication()
			->bootComponent('com_accommodation_manager')
			->getMVCFactory();
	}

	// ------------------------------------------------------------------

	/**
	 * Valid language suffixes matching DB column naming.
	 *
	 * @var string[]
	 * @since 3.2.0
	 */
	public const VALID_LANGUAGES = ['de', 'it', 'en', 'fr', 'es'];

	/**
	 * Gets the language suffix for the current frontend language.
	 * Maps Joomla language tag (e.g. 'de-DE') to DB column suffix (e.g. 'de').
	 *
	 * @return  string  Language suffix (de, it, en, fr, es)
	 *
	 * @since   3.2.0
	 */
	public static function getLanguageSuffix(): string
	{
		$langTag  = Factory::getLanguage()->getTag();
		$langCode = strtolower(substr($langTag, 0, 2));

		if (in_array($langCode, self::VALID_LANGUAGES, true))
		{
			return $langCode;
		}

		return 'de';
	}
}
