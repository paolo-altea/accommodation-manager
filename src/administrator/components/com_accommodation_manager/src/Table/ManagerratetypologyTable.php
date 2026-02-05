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
 * Managerratetypology table
 *
 * @since  3.1.0
 */
class ManagerratetypologyTable extends BaseTable
{
	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  A database connector object
	 */
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_accommodation_manager.managerratetypology';
		parent::__construct('#__accommodation_manager_rate_typologies', 'id', $db);
		$this->setColumnAlias('published', 'state');
	}
}
