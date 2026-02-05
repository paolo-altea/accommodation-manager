<?php
/**
 * @version    3.0.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2019. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\View\Managerrates;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Accomodationmanager\Component\Accommodation_manager\Administrator\Helper\Accommodation_managerHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

/**
 * View class for Rates grid.
 *
 * @since  3.0.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	public function display($tpl = null)
	{
		// Check for errors.
		$errors = $this->get('Errors');
		if ($errors && count($errors))
		{
			throw new \Exception(implode("\n", $errors));
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	protected function addToolbar()
	{
		$canDo = Accommodation_managerHelper::getActions();

		ToolbarHelper::title(Text::_('COM_ACCOMMODATION_MANAGER_TITLE_MANAGERRATES'), 'generic');

		$toolbar = Toolbar::getInstance('toolbar');

		if ($canDo->get('core.admin'))
		{
			$toolbar->preferences('com_accommodation_manager');
		}
	}
}
