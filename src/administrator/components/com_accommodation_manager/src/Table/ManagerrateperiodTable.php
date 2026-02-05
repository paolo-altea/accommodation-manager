<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\Database\DatabaseDriver;

/**
 * Managerrateperiod table
 *
 * @since 2.0.1
 */
class ManagerrateperiodTable extends Table implements VersionableTableInterface
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
	 * Get the type alias for the history table
	 *
	 * @return  string  The alias as described above
	 *
	 * @since   2.0.1
	 */
	public function getTypeAlias()
	{
		return $this->typeAlias;
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  Optional array or list of parameters to ignore
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     Table:bind
	 * @since   2.0.1
	 * @throws  \InvalidArgumentException
	 */
	public function bind($array, $ignore = '')
	{
		$date = Factory::getDate()->toSql();
		$user = Factory::getApplication()->getIdentity();

		// Handle created/modified dates and users
		if ($array['id'] == 0) {
			if (empty($array['created_by'])) {
				$array['created_by'] = $user->id;
			}
			if (empty($array['created'])) {
				$array['created'] = $date;
			}
		} else {
			$array['modified_by'] = $user->id;
			$array['modified'] = $date;
		}

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

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check function
	 *
	 * @return bool
	 */
	public function check()
	{
		// If there is an ordering column and this is a new row then get the next ordering value
		if (property_exists($this, 'ordering') && $this->id == 0) {
			$this->ordering = self::getNextOrder();
		}

		// Validate period_end >= period_start
		if (!empty($this->period_start) && !empty($this->period_end)) {
			if (strtotime($this->period_end) < strtotime($this->period_start)) {
				$this->setError(Text::_('COM_ACCOMMODATION_MANAGER_ERROR_PERIOD_END_BEFORE_START'));
				return false;
			}
		}

		return parent::check();
	}
}
