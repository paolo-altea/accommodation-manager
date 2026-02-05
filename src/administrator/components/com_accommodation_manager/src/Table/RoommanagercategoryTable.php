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
 * Roommanagercategory table
 *
 * @since  3.1.0
 */
class RoommanagercategoryTable extends BaseTable
{
	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  A database connector object
	 */
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_accommodation_manager.roommanagercategory';
		parent::__construct('#__accommodation_manager_room_categories', 'id', $db);
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
		// Support for foreign key field: room_category_parent
		if (!empty($array['room_category_parent'])) {
			if (is_array($array['room_category_parent'])) {
				$array['room_category_parent'] = implode(',', $array['room_category_parent']);
			} elseif (strrpos($array['room_category_parent'], ',') !== false) {
				$array['room_category_parent'] = explode(',', $array['room_category_parent']);
			}
		} else {
			$array['room_category_parent'] = 0;
		}
	}
}
