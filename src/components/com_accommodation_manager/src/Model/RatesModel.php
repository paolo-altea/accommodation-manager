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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Rates grid model for the public frontend.
 * Loads periods, rooms, typologies and the rates grid.
 *
 * @since  3.2.0
 */
class RatesModel extends BaseDatabaseModel
{
	/**
	 * Get all active rate periods ordered by start date.
	 *
	 * @return  array
	 *
	 * @since   3.2.0
	 */
	public function getPeriods(): array
	{
		$db   = $this->getDatabase();
		$lang = Accommodation_managerHelper::getLanguageSuffix();

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

		$db->setQuery($query);

		return $db->loadObjectList() ?: [];
	}

	/**
	 * Get all active rooms ordered by ordering.
	 *
	 * @return  array
	 *
	 * @since   3.2.0
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
	 * @since   3.2.0
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
	 * @since   3.2.0
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
