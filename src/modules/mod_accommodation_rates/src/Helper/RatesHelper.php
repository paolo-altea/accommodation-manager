<?php
/**
 * @version    1.0.0
 * @package    Mod_Accommodation_Rates
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Module\AccommodationRates\Site\Helper;

defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Site\Model\RatesModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

class RatesHelper
{
	/**
	 * Get all active rate periods ordered by start date.
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function getPeriods(): array
	{
		$hidePast = (bool) ComponentHelper::getParams('com_accommodation_manager')
			->get('rates_hide_past_periods', 0);

		return $this->getRatesModel()->getPeriods($hidePast);
	}

	/**
	 * Get all active rooms ordered by ordering.
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function getRooms(): array
	{
		return $this->getRatesModel()->getRooms();
	}

	/**
	 * Get all active rate typologies ordered by ordering.
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function getTypologies(): array
	{
		return $this->getRatesModel()->getTypologies();
	}

	/**
	 * Get the rates grid indexed as [period_id][room_id][typology_id] => rate value.
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function getRatesGrid(): array
	{
		return $this->getRatesModel()->getRatesGrid();
	}

	/**
	 * Get the component's RatesModel instance.
	 *
	 * @return  RatesModel
	 *
	 * @since   1.0.0
	 */
	private function getRatesModel(): RatesModel
	{
		return Factory::getApplication()
			->bootComponent('com_accommodation_manager')
			->getMVCFactory()
			->createModel('Rates', 'Site');
	}
}
