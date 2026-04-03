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
use Joomla\CMS\Component\ComponentHelper;
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
		$db         = $this->getDatabase();
		$categoryId = $this->getCategoryId();

		return Accommodation_managerHelper::buildRoomBaseQuery($db)
			->where($db->quoteName('a.room_category') . ' = :categoryId')
			->bind(':categoryId', $categoryId, ParameterType::INTEGER)
			->order($db->quoteName('a.ordering') . ' ASC');
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
		$priceDisplay = ComponentHelper::getParams('com_accommodation_manager')->get('rooms_price_display', 'price_from');
		$db = $this->getDatabase();

		foreach ($items as $item)
		{
			$item->gallery_items = Accommodation_managerHelper::decodeGalleryItems($item->gallery ?? null, $lang);
			$item->current_rate  = ($priceDisplay === 'current_rate') ? Accommodation_managerHelper::getCurrentRate($db, (int) $item->id) : null;
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
