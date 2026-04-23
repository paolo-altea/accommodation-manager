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
 * Roommanager table
 *
 * @since  3.1.0
 */
class RoommanagerTable extends BaseTable
{
	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  A database connector object
	 */
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_accommodation_manager.roommanager';
		parent::__construct('#__accommodation_manager_rooms', 'id', $db);
		$this->setColumnAlias('published', 'state');
	}

	/**
	 * Validate room_name uniqueness before save.
	 *
	 * @return  bool
	 *
	 * @since   3.2.0
	 */
	protected function processCheck(): bool
	{
		if (empty($this->room_name))
		{
			$this->setError(Text::_('COM_ACCOMMODATION_MANAGER_ERROR_ROOM_NAME_REQUIRED'));

			return false;
		}

		if (empty($this->room_code))
		{
			$this->setError(Text::_('COM_ACCOMMODATION_MANAGER_ERROR_ROOM_CODE_REQUIRED'));

			return false;
		}

		$db = $this->getDbo();

		// Check room_name uniqueness
		$query = $db->getQuery(true)
			->select('id')
			->from($db->quoteName($this->_tbl))
			->where($db->quoteName('room_name') . ' = :roomName')
			->bind(':roomName', $this->room_name);

		if ($this->id)
		{
			$query->where($db->quoteName('id') . ' != :id')
				->bind(':id', $this->id, \Joomla\Database\ParameterType::INTEGER);
		}

		$db->setQuery($query);

		if ($db->loadResult())
		{
			$this->setError(Text::sprintf('COM_ACCOMMODATION_MANAGER_ERROR_ROOM_NAME_DUPLICATE', $this->room_name));

			return false;
		}

		// Check room_code uniqueness
		$query = $db->getQuery(true)
			->select('id')
			->from($db->quoteName($this->_tbl))
			->where($db->quoteName('room_code') . ' = :roomCode')
			->bind(':roomCode', $this->room_code);

		if ($this->id)
		{
			$query->where($db->quoteName('id') . ' != :idCode')
				->bind(':idCode', $this->id, \Joomla\Database\ParameterType::INTEGER);
		}

		$db->setQuery($query);

		if ($db->loadResult())
		{
			$this->setError(Text::sprintf('COM_ACCOMMODATION_MANAGER_ERROR_ROOM_CODE_DUPLICATE', $this->room_code));

			return false;
		}

		return true;
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
		// Normalize empty strings to NULL for nullable numeric columns
		foreach (['room_surface', 'room_price_from'] as $numField) {
			if (isset($array[$numField]) && $array[$numField] === '') {
				$array[$numField] = null;
			}
		}

		// Convert room_gallery subform array to JSON
		if (isset($array['room_gallery']) && is_array($array['room_gallery'])) {
			$array['room_gallery'] = json_encode($array['room_gallery']);
		}

		// Support for foreign key field: room_category
		if (!empty($array['room_category'])) {
			if (is_array($array['room_category'])) {
				$array['room_category'] = implode(',', $array['room_category']);
			} elseif (strrpos($array['room_category'], ',') !== false) {
				$array['room_category'] = explode(',', $array['room_category']);
			}
		} else {
			$array['room_category'] = 0;
		}
	}
}
