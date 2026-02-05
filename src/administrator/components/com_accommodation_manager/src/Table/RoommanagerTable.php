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
use Joomla\CMS\Table\Table;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\Database\DatabaseDriver;

/**
 * Roommanager table
 *
 * @since 2.0.1
 */
class RoommanagerTable extends Table implements VersionableTableInterface
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

		return parent::check();
	}
}
