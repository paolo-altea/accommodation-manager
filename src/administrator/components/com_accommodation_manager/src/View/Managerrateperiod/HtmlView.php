<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\View\Managerrateperiod;

\defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Administrator\View\BaseEditView;

/**
 * View class for a single Managerrateperiod.
 *
 * @since  3.1.0
 */
class HtmlView extends BaseEditView
{
	protected string $taskPrefix = 'managerrateperiod';
	protected string $titleKey = 'COM_ACCOMMODATION_MANAGER_TITLE_MANAGERRATEPERIOD';
	protected string $typeAlias = 'com_accommodation_manager.managerrateperiod';
}
