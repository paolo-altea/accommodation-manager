<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\Controller;

\defined('_JEXEC') or die;

/**
 * Managerroomcategories list controller class.
 *
 * @since  3.1.0
 */
class ManagerroomcategoriesController extends BaseListController
{
	protected string $listView = 'managerroomcategories';
	protected string $modelName = 'Roommanagercategory';
}
