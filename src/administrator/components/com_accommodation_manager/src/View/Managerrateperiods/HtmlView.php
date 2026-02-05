<?php
/**
 * @version    CVS: 2.0.1
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2019. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\View\Managerrateperiods;
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Accomodationmanager\Component\Accommodation_manager\Administrator\Helper\Accommodation_managerHelper;
use \Joomla\CMS\Toolbar\Toolbar;
use \Joomla\CMS\Toolbar\ToolbarHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\Component\Content\Administrator\Extension\ContentComponent;
use \Joomla\CMS\Form\Form;
use \Joomla\CMS\HTML\Helpers\Sidebar;
/**
 * View class for a list of Managerrateperiods.
 *
 * @since  2.0.1
 */
class HtmlView extends BaseHtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * @var  array  Workflow transitions (empty if workflows not used)
	 */
	protected $transitions = [];

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors));
		}

		$this->addToolbar();

		$this->sidebar = Sidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   2.0.1
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = Accommodation_managerHelper::getActions();

		ToolbarHelper::title(Text::_('COM_ACCOMMODATION_MANAGER_TITLE_MANAGERRATEPERIODS'), "generic");

		$toolbar = Toolbar::getInstance('toolbar');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/src/View/Managerrateperiods';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				$toolbar->addNew('managerrateperiod.add');
			}
		}

		if ($canDo->get('core.edit.state')  || count($this->transitions))
		{
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('fas fa-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();

			if (isset($this->items[0]->state))
			{
				$childBar->publish('managerrateperiods.publish')->listCheck(true);
				$childBar->unpublish('managerrateperiods.unpublish')->listCheck(true);
				$childBar->archive('managerrateperiods.archive')->listCheck(true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				$toolbar->delete('managerrateperiods.delete')
				->text('JTOOLBAR_EMPTY_TRASH')
				->message('JGLOBAL_CONFIRM_DELETE')
				->listCheck(true);
			}

			$childBar->standardButton('duplicate')
				->text('JTOOLBAR_DUPLICATE')
				->icon('fas fa-copy')
				->task('managerrateperiods.duplicate')
				->listCheck(true);

			if (isset($this->items[0]->checked_out))
			{
				$childBar->checkin('managerrateperiods.checkin')->listCheck(true);
			}

			if (isset($this->items[0]->state))
			{
				$childBar->trash('managerrateperiods.trash')->listCheck(true);
			}
		}

		

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{

			if ($this->state->get('filter.state') == ContentComponent::CONDITION_TRASHED && $canDo->get('core.delete'))
			{
				$toolbar->delete('managerrateperiods.delete')
					->text('JTOOLBAR_EMPTY_TRASH')
					->message('JGLOBAL_CONFIRM_DELETE')
					->listCheck(true);
			}
		}

		if ($canDo->get('core.admin'))
		{
			$toolbar->preferences('com_accommodation_manager');
		}

		// Set sidebar action
		Sidebar::setAction('index.php?option=com_accommodation_manager&view=managerrateperiods');
	}
	
	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => Text::_('JGRID_HEADING_ID'),
			'a.`ordering`' => Text::_('JGRID_HEADING_ORDERING'),
			'a.`state`' => Text::_('JSTATUS'),
			'a.`period_start`' => Text::_('COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_START'),
			'a.`period_end`' => Text::_('COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_END'),
			'a.`period_title_de`' => Text::_('COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_TITLE_DE'),
			'a.`period_title_it`' => Text::_('COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_TITLE_IT'),
			'a.`period_title_en`' => Text::_('COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_TITLE_EN'),
			'a.`period_title_fr`' => Text::_('COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_TITLE_FR'),
			'a.`period_title_es`' => Text::_('COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_TITLE_ES'),
		);
	}

	/**
	 * Check if state is set
	 *
	 * @param   mixed  $state  State
	 *
	 * @return bool
	 */
	public function getState($state)
	{
		return isset($this->state->{$state}) ? $this->state->{$state} : false;
	}
}
