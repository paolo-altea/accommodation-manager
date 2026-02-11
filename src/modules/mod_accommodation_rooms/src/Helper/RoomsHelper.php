<?php
/**
 * @version    1.0.0
 * @package    Mod_Accommodation_Rooms
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Module\AccommodationRooms\Site\Helper;

defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Registry\Registry;

class RoomsHelper implements DatabaseAwareInterface
{
	use DatabaseAwareTrait;

	/**
	 * Get rooms from the database.
	 *
	 * @param   Registry  $params  Module parameters
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function getRooms(Registry $params): array
	{
		$db   = $this->getDatabase();
		$lang = Accommodation_managerHelper::getLanguageSuffix();

		$query = $db->getQuery(true)
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
			])
			->from($db->quoteName('#__accommodation_manager_rooms', 'a'))
			->join('LEFT', $db->quoteName('#__accommodation_manager_room_categories', 'c')
				. ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.room_category'))
			->where($db->quoteName('a.state') . ' = 1');

		// Ordering
		$orderCol = $params->get('ordering', 'a.ordering');
		$orderDir = strtoupper($params->get('ordering_direction', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

		$allowedColumns = ['a.ordering', 'title', 'a.room_name'];

		if (!in_array($orderCol, $allowedColumns, true))
		{
			$orderCol = 'a.ordering';
		}

		if ($orderCol === 'title')
		{
			$orderCol = 'a.room_title_' . $lang;
		}

		$query->order($db->quoteName($orderCol) . ' ' . $orderDir);

		// Filter by category
		$categoryId = (int) $params->get('category_id', 0);

		if ($categoryId > 0)
		{
			$query->where($db->quoteName('a.room_category') . ' = :catId')
				->bind(':catId', $categoryId, \Joomla\Database\ParameterType::INTEGER);
		}

		// Limit
		$count = (int) $params->get('count', 0);

		if ($count > 0)
		{
			$query->setLimit($count);
		}

		$db->setQuery($query);
		$items = $db->loadObjectList();

		// Decode gallery JSON
		foreach ($items as $item)
		{
			$item->gallery_items = Accommodation_managerHelper::decodeGalleryItems($item->gallery ?? null, $lang);
		}

		return $items;
	}
}
