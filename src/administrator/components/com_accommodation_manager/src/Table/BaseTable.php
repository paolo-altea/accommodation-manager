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

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Versioning\VersionableTableInterface;

/**
 * Base table class for Accommodation Manager.
 * Provides common functionality for all entity tables.
 *
 * @since  3.1.0
 */
abstract class BaseTable extends Table implements VersionableTableInterface
{
	/**
	 * Get the type alias for the history table.
	 *
	 * @return  string  The alias as described above
	 *
	 * @since   3.1.0
	 */
	public function getTypeAlias(): string
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
	 * @since   3.1.0
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

		// Allow child classes to process their specific fields
		$this->processBind($array);

		return parent::bind($array, $ignore);
	}

	/**
	 * Hook method for child classes to process entity-specific fields in bind().
	 * Override this method to handle FK fields, JSON encoding, etc.
	 *
	 * @param   array  &$array  The data array (passed by reference)
	 *
	 * @return  void
	 *
	 * @since   3.1.0
	 */
	protected function processBind(array &$array): void
	{
		// Default implementation does nothing.
		// Override in child classes for entity-specific logic.
	}

	/**
	 * Overloaded check function.
	 *
	 * @return  bool
	 *
	 * @since   3.1.0
	 */
	public function check(): bool
	{
		// If there is an ordering column and this is a new row then get the next ordering value
		if (property_exists($this, 'ordering') && $this->id == 0) {
			$this->ordering = self::getNextOrder();
		}

		// Allow child classes to perform additional validation
		if (!$this->processCheck()) {
			return false;
		}

		return parent::check();
	}

	/**
	 * Hook method for child classes to perform additional validation in check().
	 * Override this method to add entity-specific validation rules.
	 *
	 * @return  bool  True if validation passes, false otherwise
	 *
	 * @since   3.1.0
	 */
	protected function processCheck(): bool
	{
		// Default implementation passes validation.
		// Override in child classes for entity-specific validation.
		return true;
	}
}
