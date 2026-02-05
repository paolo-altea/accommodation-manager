<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\Table;

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;

/**
 * Managerrateperiod table
 *
 * @since  3.1.0
 */
class ManagerrateperiodTable extends BaseTable
{
	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  A database connector object
	 */
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_accommodation_manager.managerrateperiod';
		parent::__construct('#__accommodation_manager_rate_periods', 'id', $db);
		$this->setColumnAlias('published', 'state');
	}

	/**
	 * Process entity-specific fields in bind().
	 *
	 * @param   array  &$array  The data array
	 *
	 * @return  void
	 *
	 * @since   3.1.0
	 */
	protected function processBind(array &$array): void
	{
		// Support for empty date field: period_start
		if ($array['period_start'] == '0000-00-00' || empty($array['period_start'])) {
			$array['period_start'] = null;
			$this->period_start = null;
		}

		// Support for empty date field: period_end
		if ($array['period_end'] == '0000-00-00' || empty($array['period_end'])) {
			$array['period_end'] = null;
			$this->period_end = null;
		}
	}

	/**
	 * Perform additional validation in check().
	 *
	 * @return  bool  True if validation passes, false otherwise
	 *
	 * @since   3.1.0
	 */
	protected function processCheck(): bool
	{
		// Validate period_end >= period_start
		if (!empty($this->period_start) && !empty($this->period_end)) {
			if (strtotime($this->period_end) < strtotime($this->period_start)) {
				$this->setError(Text::_('COM_ACCOMMODATION_MANAGER_ERROR_PERIOD_END_BEFORE_START'));
				return false;
			}
		}

		return true;
	}
}
