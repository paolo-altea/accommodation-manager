<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\View\Managerratetypology;

defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Administrator\Helper\Accommodation_managerHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for a single Managerratetypology.
 *
 * @since  2.0.1
 */
class HtmlView extends BaseHtmlView
{
	protected $state;

	protected $item;

	protected $form;

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
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors));
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user  = Factory::getApplication()->getIdentity();
		$isNew = ($this->item->id == 0);
		$checkedOut = isset($this->item->checked_out) && $this->item->checked_out != 0 && $this->item->checked_out != $user->get('id');
		$canDo = Accommodation_managerHelper::getActions();
		$toolbar = Toolbar::getInstance();

		ToolbarHelper::title(Text::_('COM_ACCOMMODATION_MANAGER_TITLE_MANAGERRATETYPOLOGY'), 'generic');

		// Build the save group button
		if (!$checkedOut && ($canDo->get('core.edit') || $canDo->get('core.create')))
		{
			$saveGroup = $toolbar->dropdownButton('save-group');

			$saveGroup->configure(
				function (Toolbar $childBar) use ($canDo, $checkedOut, $isNew)
				{
					// Save (apply)
					$childBar->apply('managerratetypology.apply');

					// Save & Close
					$childBar->save('managerratetypology.save');

					// Save & New
					if ($canDo->get('core.create'))
					{
						$childBar->save2new('managerratetypology.save2new');
					}

					// Save as Copy
					if (!$isNew && $canDo->get('core.create'))
					{
						$childBar->save2copy('managerratetypology.save2copy');
					}
				}
			);
		}

		// Button for version control
		if ($this->state->params->get('save_history', 1) && $user->authorise('core.edit') && !$isNew)
		{
			ToolbarHelper::versions('com_accommodation_manager.managerratetypology', $this->item->id);
		}

		// Cancel/Close button
		if ($isNew)
		{
			$toolbar->cancel('managerratetypology.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			$toolbar->cancel('managerratetypology.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
