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
 * Category model: rooms filtered by a single category.
 *
 * @since  3.2.0
 */
class CategoryModel extends ListModel
{
	/**
	 * @var  object|null  Cached category data
	 */
	protected $_category;

	/**
	 * Build the query to retrieve published rooms for a specific category.
	 *
	 * @return  \Joomla\Database\QueryInterface
	 *
	 * @since   3.2.0
	 */
	protected function getListQuery()
	{
		$db   = $this->getDatabase();
		$lang = Accommodation_managerHelper::getLanguageSuffix();

		$categoryId = $this->getCategoryId();

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
			->where($db->quoteName('a.room_category') . ' = :categoryId')
			->bind(':categoryId', $categoryId, ParameterType::INTEGER)
			->order($db->quoteName('a.ordering') . ' ASC');

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
			$item->gallery_items = Accommodation_managerHelper::decodeGalleryItems($item->gallery ?? null, $lang);
		}

		return $items;
	}

	/**
	 * Get the category data (name, description, image).
	 *
	 * @return  object|null
	 *
	 * @since   3.2.0
	 */
	public function getCategory()
	{
		if ($this->_category !== null)
		{
			return $this->_category;
		}

		$categoryId = $this->getCategoryId();

		if (!$categoryId)
		{
			return null;
		}

		$db   = $this->getDatabase();
		$lang = Accommodation_managerHelper::getLanguageSuffix();

		$query = $db->getQuery(true)
			->select([
				$db->quoteName('id'),
				$db->quoteName('room_category_name_' . $lang, 'name'),
				$db->quoteName('room_category_description_' . $lang, 'description'),
				$db->quoteName('room_category_image', 'image'),
				$db->quoteName('room_category_image_alt_' . $lang, 'image_alt'),
			])
			->from($db->quoteName('#__accommodation_manager_room_categories'))
			->where($db->quoteName('state') . ' = 1')
			->where($db->quoteName('id') . ' = :id')
			->bind(':id', $categoryId, ParameterType::INTEGER);

		$db->setQuery($query);
		$this->_category = $db->loadObject();

		return $this->_category;
	}

	/**
	 * Get the category ID from input or menu item params.
	 *
	 * @return  int
	 *
	 * @since   3.2.0
	 */
	private function getCategoryId(): int
	{
		$app = Factory::getApplication();
		$id  = $app->getInput()->getInt('id', 0);

		if (!$id)
		{
			$id = (int) $app->getParams('com_accommodation_manager')->get('id', 0);
		}

		return $id;
	}
}
