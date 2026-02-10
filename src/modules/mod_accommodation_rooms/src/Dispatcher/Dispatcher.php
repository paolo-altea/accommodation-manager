<?php
/**
 * @version    1.0.0
 * @package    Mod_Accommodation_Rooms
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Module\AccommodationRooms\Site\Dispatcher;

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
	use HelperFactoryAwareTrait;

	/**
	 * Returns the layout data.
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	protected function getLayoutData(): array
	{
		$data   = parent::getLayoutData();
		$params = $data['params'];
		$helper = $this->getHelperFactory()->getHelper('RoomsHelper');

		$data['items'] = $helper->getRooms($params);

		return $data;
	}
}
