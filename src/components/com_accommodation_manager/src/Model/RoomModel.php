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

		$db = $this->getDatabase();

		$query = Accommodation_managerHelper::buildRoomBaseQuery($db)
			->where($db->quoteName('a.id') . ' = :id')
			->bind(':id', $id, ParameterType::INTEGER);

		$db->setQuery($query);
		$item = $db->loadObject();

		if ($item)
		{
			$item->gallery_items = Accommodation_managerHelper::decodeGalleryItems(
				$item->gallery ?? null,
				Accommodation_managerHelper::getLanguageSuffix()
			);

			$priceDisplay = ComponentHelper::getParams('com_accommodation_manager')->get('rooms_price_display', 'price_from');
			$item->current_rate = ($priceDisplay === 'current_rate') ? Accommodation_managerHelper::getCurrentRate($db, (int) $item->id) : null;
		}

		$this->_item = $item;

		return $this->_item;
	}
}
