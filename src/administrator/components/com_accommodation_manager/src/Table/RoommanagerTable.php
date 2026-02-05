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
