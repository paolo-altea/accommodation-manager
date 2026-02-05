<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\View\Managerratetypology;

\defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Administrator\View\BaseEditView;

/**
 * View class for a single Managerratetypology.
 *
 * @since  3.1.0
 */
class HtmlView extends BaseEditView
{
	protected string $taskPrefix = 'managerratetypology';
	protected string $titleKey = 'COM_ACCOMMODATION_MANAGER_TITLE_MANAGERRATETYPOLOGY';
	protected string $typeAlias = 'com_accommodation_manager.managerratetypology';
}
