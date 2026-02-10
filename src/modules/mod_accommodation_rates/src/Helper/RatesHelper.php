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

use Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;

class RatesHelper implements DatabaseAwareInterface
{
	use DatabaseAwareTrait;

	/**
	 * Get component params for rates configuration.
	 *
	 * @return  \Joomla\Registry\Registry
	 *
	 * @since   1.0.0
	 */
	private function getComponentParams(): \Joomla\Registry\Registry
	{
		return ComponentHelper::getParams('com_accommodation_manager');
	}

	/**
	 * Get all active rate periods ordered by start date.
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function getPeriods(): array
	{
		$db       = $this->getDatabase();
		$lang     = Accommodation_managerHelper::getLanguageSuffix();
		$hidePast = (bool) $this->getComponentParams()->get('rates_hide_past_periods', 0);

		$query = $db->getQuery(true)
			->select([
				$db->quoteName('id'),
				$db->quoteName('period_start'),
				$db->quoteName('period_end'),
				$db->quoteName('period_title_' . $lang, 'title'),
			])
			->from($db->quoteName('#__accommodation_manager_rate_periods'))
			->where($db->quoteName('state') . ' = 1')
			->order($db->quoteName('period_start') . ' ASC');

		if ($hidePast)
		{
			$query->where($db->quoteName('period_end') . ' >= CURDATE()');
		}

		$db->setQuery($query);

		return $db->loadObjectList() ?: [];
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
		$db   = $this->getDatabase();
		$lang = Accommodation_managerHelper::getLanguageSuffix();

		$query = $db->getQuery(true)
			->select([
				$db->quoteName('id'),
				$db->quoteName('room_name'),
				$db->quoteName('room_title_' . $lang, 'title'),
			])
			->from($db->quoteName('#__accommodation_manager_rooms'))
			->where($db->quoteName('state') . ' = 1')
			->order($db->quoteName('ordering') . ' ASC');

		$db->setQuery($query);

		return $db->loadObjectList() ?: [];
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
		$db   = $this->getDatabase();
		$lang = Accommodation_managerHelper::getLanguageSuffix();

		$query = $db->getQuery(true)
			->select([
				$db->quoteName('id'),
				$db->quoteName('rate_typology_' . $lang, 'title'),
				$db->quoteName('rate_typology_title', 'title_fallback'),
			])
			->from($db->quoteName('#__accommodation_manager_rate_typologies'))
			->where($db->quoteName('state') . ' = 1')
			->order($db->quoteName('ordering') . ' ASC');

		$db->setQuery($query);

		return $db->loadObjectList() ?: [];
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
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->select([
				$db->quoteName('period_id'),
				$db->quoteName('room_id'),
				$db->quoteName('typology_id'),
				$db->quoteName('rate'),
			])
			->from($db->quoteName('#__accommodation_manager_rates'))
			->where($db->quoteName('state') . ' = 1');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$grid = [];

		foreach ($rows as $row)
		{
			$grid[(int) $row->period_id][(int) $row->room_id][(int) $row->typology_id] = $row->rate;
		}

		return $grid;
	}
}
