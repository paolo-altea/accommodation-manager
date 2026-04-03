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
use Joomla\CMS\MVC\Model\ListModel;

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
		$db = $this->getDatabase();

		return Accommodation_managerHelper::buildRoomBaseQuery($db)
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
}
