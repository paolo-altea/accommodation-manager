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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\ParameterType;

/**
 * Single room model for the public frontend.
 *
 * @since  3.2.0
 */
class RoomModel extends BaseDatabaseModel
{
	/**
	 * @var  object|null  Cached item
	 */
	protected $_item;

	/**
	 * Get a single published room with all localized fields.
	 *
	 * @param   int|null  $pk  Room ID. If null, taken from input.
	 *
	 * @return  object|null
	 *
	 * @since   3.2.0
	 */
	public function getItem($pk = null)
	{
		if ($this->_item !== null)
		{
			return $this->_item;
		}

		$app = Factory::getApplication();
		$id  = $pk ?: $app->getInput()->getInt('id', 0);

		if (!$id)
		{
			return null;
		}

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
				$db->quoteName('a.room_title_' . $lang, 'title'),
				$db->quoteName('a.room_intro_' . $lang, 'intro'),
				$db->quoteName('a.room_description_' . $lang, 'description'),
				$db->quoteName('a.room_thumbnail', 'thumbnail'),
				$db->quoteName('a.room_thumbnail_alt_' . $lang, 'thumbnail_alt'),
				$db->quoteName('a.room_floor_plan', 'floor_plan'),
				$db->quoteName('a.room_floor_plan_alt_' . $lang, 'floor_plan_alt'),
				$db->quoteName('a.room_gallery', 'gallery'),
				$db->quoteName('a.room_video', 'video'),
				$db->quoteName('c.room_category_name_' . $lang, 'category_name'),
			])
			->from($db->quoteName('#__accommodation_manager_rooms', 'a'))
			->join('LEFT', $db->quoteName('#__accommodation_manager_room_categories', 'c')
				. ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.room_category'))
			->where($db->quoteName('a.state') . ' = 1')
			->where($db->quoteName('a.id') . ' = :id')
			->bind(':id', $id, ParameterType::INTEGER);

		$db->setQuery($query);
		$item = $db->loadObject();

		if ($item)
		{
			$item->gallery_items = Accommodation_managerHelper::decodeGalleryItems($item->gallery ?? null, $lang);
		}

		$this->_item = $item;

		return $this->_item;
	}
}
