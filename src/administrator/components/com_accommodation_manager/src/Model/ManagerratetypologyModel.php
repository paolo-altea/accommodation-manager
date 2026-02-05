<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;

/**
 * Managerratetypology model.
 *
 * @since  3.1.0
 */
class ManagerratetypologyModel extends BaseItemModel
{
	/**
	 * @var    string  Alias to manage history control
	 * @since  3.1.0
	 */
	public $typeAlias = 'com_accommodation_manager.managerratetypology';

	/**
	 * @var    string  The database table name for ordering queries
	 * @since  3.1.0
	 */
	protected string $tableName = '#__accommodation_manager_rate_typologies';

	/**
	 * Returns a reference to a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table  A database object
	 *
	 * @since   3.1.0
	 */
	public function getTable($type = 'Managerratetypology', $prefix = 'Administrator', $config = [])
	{
		return parent::getTable($type, $prefix, $config);
	}
}
