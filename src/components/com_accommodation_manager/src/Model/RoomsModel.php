<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Site\Model;

defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

/**
 * Rooms list model for the public frontend.
 * Returns all room data in the current language.
 *
 * @since  3.2.0
 */
class RoomsModel extends ListModel
{
	/**
	 * Build the query to retrieve published rooms with all fields.
	 *
	 * @return  \Joomla\Database\QueryInterface
	 *
	 * @since   3.2.0
	 */
	protected function getListQuery()
	{
		$db   = $this->getDatabase();
		$lang = Accommodation_managerHelper::getLanguageSuffix();
		$app  = Factory::getApplication();

		$query = $db->getQuery(true)
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
			->where($db->quoteName('a.state') . ' = 1')
			->order($db->quoteName('a.ordering') . ' ASC');

		// Filter by category: from URL param or menu item param
		$categoryId = $app->getInput()->getInt('category_id', 0);

		if (!$categoryId)
		{
			$categoryId = (int) $app->getParams('com_accommodation_manager')->get('category_id', 0);
		}

		if ($categoryId > 0)
		{
			$query->where($db->quoteName('a.room_category') . ' = :categoryId')
				->bind(':categoryId', $categoryId, ParameterType::INTEGER);
		}

		return $query;
	}

	/**
	 * Override to decode gallery JSON after loading items.
	 *
	 * @return  array
	 *
	 * @since   3.2.0
	 */
	public function getItems()
	{
		$items = parent::getItems();
		$lang  = Accommodation_managerHelper::getLanguageSuffix();

		foreach ($items as $item)
		{
			// Decode gallery JSON
			$item->gallery_items = [];

			if (!empty($item->gallery))
			{
				$decoded = json_decode($item->gallery, true);

				if (is_array($decoded))
				{
					foreach ($decoded as $galleryItem)
					{
						$item->gallery_items[] = (object) [
							'image'        => $galleryItem['image'] ?? '',
							'image_mobile' => $galleryItem['image_mobile'] ?? '',
							'alt'          => $galleryItem['alt_' . $lang] ?? '',
						];
					}
				}
			}
		}

		return $items;
	}
}
