<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Site\Helper;

defined('_JEXEC') or die;

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
			$year       = (int) date('Y', strtotime($period->period_start));
			$month      = (int) date('m', strtotime($period->period_start));
			$day        = (int) date('d', strtotime($period->period_start));
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
	 * Valid language suffixes matching DB column naming
	 *
	 * @var array
	 */
	private static array $validLanguages = ['de', 'it', 'en', 'fr', 'es'];

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

		if (in_array($langCode, self::$validLanguages, true))
		{
			return $langCode;
		}

		return 'de';
	}
}
