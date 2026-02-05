<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\View\Managerroomcategories;

\defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Administrator\View\BaseListView;

/**
 * View class for a list of Managerroomcategories.
 *
 * @since  3.1.0
 */
class HtmlView extends BaseListView
{
	protected string $taskPrefix = 'managerroomcategories';
	protected string $addTask = 'roommanagercategory.add';
	protected string $titleKey = 'COM_ACCOMMODATION_MANAGER_TITLE_MANAGERROOMCATEGORIES';
}
