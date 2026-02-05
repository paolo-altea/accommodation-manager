<?php
/**
 * @version    3.0.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2019. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\Model;
// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Methods supporting a list of Managerrates records.
 *
 * @since  2.0.1
 */
class ManagerratesModel extends ListModel
{
	/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'ordering', 'a.ordering',
				'state', 'a.state',
				'created_by', 'a.created_by',
				'room_id', 'a.room_id',
				'period_id', 'a.period_id',
				'typology_id', 'a.typology_id',
				'rate', 'a.rate',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState("a.id", "ASC");

		$context = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $context);

		// Split context into component and optional section
		$parts = FieldsHelper::extract($context);

		if ($parts)
		{
			$this->setState('filter.component', $parts[0]);
			$this->setState('filter.section', $parts[1]);
		}
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string A store id.
	 *
	 * @since   2.0.1
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		
		return parent::getStoreId($id);
		
	}

	/**
	 * Get active rate periods with pagination.
	 *
	 * @return  array
	 *
	 * @since   3.0.0
	 */
	public function getPeriods(): array
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select([
				$db->quoteName('id'),
				$db->quoteName('period_start'),
				$db->quoteName('period_end'),
				$db->quoteName('period_title_de'),
				$db->quoteName('period_title_it'),
				$db->quoteName('period_title_en'),
			])
			->from($db->quoteName('#__accommodation_manager_rate_periods'))
			->where($db->quoteName('state') . ' = 1')
			->order($db->quoteName('period_start') . ' ASC');

		// Apply pagination
		$start = $this->getStart();
		$limit = $this->getState('list.limit', 20);

		return $db->setQuery($query, $start, $limit)->loadObjectList() ?: [];
	}

	/**
	 * Get total count of active periods for pagination.
	 *
	 * @return  int
	 *
	 * @since   3.0.0
	 */
	public function getPeriodsTotal(): int
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__accommodation_manager_rate_periods'))
			->where($db->quoteName('state') . ' = 1');

		return (int) $db->setQuery($query)->loadResult();
	}

	/**
	 * Get the pagination object for periods.
	 *
	 * @return  \Joomla\CMS\Pagination\Pagination
	 *
	 * @since   3.0.0
	 */
	public function getPeriodsPagination(): \Joomla\CMS\Pagination\Pagination
	{
		$total = $this->getPeriodsTotal();
		$start = $this->getStart();
		$limit = $this->getState('list.limit', 20);

		return new \Joomla\CMS\Pagination\Pagination($total, $start, $limit);
	}

	/**
	 * Get all active rooms ordered by ordering.
	 *
	 * @return  array
	 *
	 * @since   3.0.0
	 */
	public function getRooms(): array
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select([
				$db->quoteName('id'),
				$db->quoteName('room_name'),
				$db->quoteName('room_code'),
			])
			->from($db->quoteName('#__accommodation_manager_rooms'))
			->where($db->quoteName('state') . ' = 1')
			->order($db->quoteName('ordering') . ' ASC');

		return $db->setQuery($query)->loadObjectList() ?: [];
	}

	/**
	 * Get all active rate typologies ordered by ordering.
	 *
	 * @return  array
	 *
	 * @since   3.0.0
	 */
	public function getTypologies(): array
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select([
				$db->quoteName('id'),
				$db->quoteName('rate_typology_title'),
				$db->quoteName('rate_typology_de'),
				$db->quoteName('rate_typology_it'),
				$db->quoteName('rate_typology_en'),
				$db->quoteName('rate_typology_fr'),
				$db->quoteName('rate_typology_es'),
			])
			->from($db->quoteName('#__accommodation_manager_rate_typologies'))
			->where($db->quoteName('state') . ' = 1')
			->order($db->quoteName('ordering') . ' ASC');

		return $db->setQuery($query)->loadObjectList() ?: [];
	}

	/**
	 * Get all rates indexed by period_id, room_id, typology_id.
	 *
	 * @return  array  Multidimensional array [period_id][room_id][typology_id] => ['id' => x, 'rate' => y]
	 *
	 * @since   3.0.0
	 */
	public function getRatesGrid(): array
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select([
				$db->quoteName('id'),
				$db->quoteName('room_id'),
				$db->quoteName('period_id'),
				$db->quoteName('typology_id'),
				$db->quoteName('rate'),
			])
			->from($db->quoteName('#__accommodation_manager_rates'))
			->where($db->quoteName('state') . ' = 1');

		$rates = $db->setQuery($query)->loadObjectList() ?: [];

		$indexed = [];
		foreach ($rates as $rate) {
			$indexed[$rate->period_id][$rate->room_id][$rate->typology_id] = [
				'id'   => $rate->id,
				'rate' => $rate->rate,
			];
		}

		return $indexed;
	}

	/**
	 * Save rates grid (bulk upsert).
	 *
	 * @param   array  $ratesData  Array structured as [period_id][room_id][typology_id] => rate_value
	 * @param   int    $userId     The user ID performing the save
	 *
	 * @return  bool
	 *
	 * @throws  \RuntimeException
	 *
	 * @since   3.0.0
	 */
	public function saveRatesGrid(array $ratesData, int $userId): bool
	{
		$db = $this->getDatabase();

		// Get existing rates indexed by composite key
		$existingRates = $this->getExistingRatesMap();

		// Collect all combinations from the submitted data
		$submittedKeys = [];

		$db->transactionStart();

		try {
			$ordering = $this->getMaxOrdering();

			foreach ($ratesData as $periodId => $periods) {
				$periodId = (int) $periodId;

				foreach ($periods as $roomId => $rooms) {
					$roomId = (int) $roomId;

					foreach ($rooms as $typologyId => $rateValue) {
						$typologyId = (int) $typologyId;
						$rateValue  = trim($rateValue);
						$key        = "{$periodId}_{$roomId}_{$typologyId}";

						$submittedKeys[$key] = true;

						// Treat empty string or "--" as "not available" - delete the record
						if ($rateValue === '' || $rateValue === '--') {
							// Empty or not available: delete if exists
							if (isset($existingRates[$key])) {
								$this->deleteRate((int) $existingRates[$key]['id']);
							}
						} elseif (isset($existingRates[$key])) {
							// Existing rate: update only if changed
							if ($existingRates[$key]['rate'] !== $rateValue) {
								$this->updateRate((int) $existingRates[$key]['id'], $rateValue, $userId);
							}
						} else {
							// New rate: insert
							$ordering++;
							$this->insertRate($periodId, $roomId, $typologyId, $rateValue, $userId, $ordering);
						}
					}
				}
			}

			$db->transactionCommit();
		} catch (\Exception $e) {
			$db->transactionRollback();
			throw new \RuntimeException(Text::sprintf('COM_ACCOMMODATION_MANAGER_RATES_SAVE_ERROR', $e->getMessage()), 500, $e);
		}

		// Clean cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Get existing rates as a map keyed by "periodId_roomId_typologyId".
	 *
	 * @return  array
	 *
	 * @since   3.0.0
	 */
	private function getExistingRatesMap(): array
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select([
				$db->quoteName('id'),
				$db->quoteName('period_id'),
				$db->quoteName('room_id'),
				$db->quoteName('typology_id'),
				$db->quoteName('rate'),
			])
			->from($db->quoteName('#__accommodation_manager_rates'));

		$rates = $db->setQuery($query)->loadObjectList() ?: [];

		$map = [];
		foreach ($rates as $rate) {
			$key = "{$rate->period_id}_{$rate->room_id}_{$rate->typology_id}";
			$map[$key] = [
				'id'   => $rate->id,
				'rate' => $rate->rate,
			];
		}

		return $map;
	}

	/**
	 * Get the maximum ordering value.
	 *
	 * @return  int
	 *
	 * @since   3.0.0
	 */
	private function getMaxOrdering(): int
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('MAX(' . $db->quoteName('ordering') . ')')
			->from($db->quoteName('#__accommodation_manager_rates'));

		return (int) $db->setQuery($query)->loadResult();
	}

	/**
	 * Insert a new rate.
	 *
	 * @param   int     $periodId    Period ID
	 * @param   int     $roomId      Room ID
	 * @param   int     $typologyId  Typology ID
	 * @param   string  $rate        Rate value
	 * @param   int     $userId      User ID
	 * @param   int     $ordering    Ordering value
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	private function insertRate(int $periodId, int $roomId, int $typologyId, string $rate, int $userId, int $ordering): void
	{
		$db = $this->getDatabase();

		$record = (object) [
			'period_id'   => $periodId,
			'room_id'     => $roomId,
			'typology_id' => $typologyId,
			'rate'        => $rate,
			'state'       => 1,
			'ordering'    => $ordering,
			'created_by'  => $userId,
			'checked_out' => null,
			'checked_out_time' => null,
		];

		$db->insertObject('#__accommodation_manager_rates', $record);
	}

	/**
	 * Update an existing rate.
	 *
	 * @param   int     $id      Rate ID
	 * @param   string  $rate    New rate value
	 * @param   int     $userId  User ID (for modified_by when column exists)
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	private function updateRate(int $id, string $rate, int $userId): void
	{
		$db = $this->getDatabase();

		$record = (object) [
			'id'   => $id,
			'rate' => $rate,
		];

		$db->updateObject('#__accommodation_manager_rates', $record, 'id');
	}

	/**
	 * Delete a rate by ID.
	 *
	 * @param   int  $id  Rate ID
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	private function deleteRate(int $id): void
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__accommodation_manager_rates'))
			->where($db->quoteName('id') . ' = :id')
			->bind(':id', $id);

		$db->setQuery($query)->execute();
	}
}
