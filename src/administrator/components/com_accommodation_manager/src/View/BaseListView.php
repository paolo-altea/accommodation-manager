<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\View;

\defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Administrator\Helper\Accommodation_managerHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;

/**
 * Base list view class for Accommodation Manager.
 *
 * @since  3.1.0
 */
abstract class BaseListView extends BaseHtmlView
{
	protected $items;
	protected $pagination;
	protected $state;
	public $filterForm;
	public $activeFilters;

	/**
	 * Workflow transitions (empty if workflows not used)
	 *
	 * @var    array
	 * @since  3.1.0
	 */
	protected $transitions = [];

	/**
	 * The task prefix for this view (e.g., 'roomsmanager', 'managerroomcategories')
	 *
	 * @var    string
	 * @since  3.1.0
	 */
	protected string $taskPrefix = '';

	/**
	 * The add task name (e.g., 'roommanager.add', 'roommanagercategory.add')
	 *
	 * @var    string
	 * @since  3.1.0
	 */
	protected string $addTask = '';

	/**
	 * The title language key for this view
	 *
	 * @var    string
	 * @since  3.1.0
	 */
	protected string $titleKey = '';

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return  void
	 *
	 * @throws  \Exception
	 * @since   3.1.0
	 */
	public function display($tpl = null)
	{
		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		if (count($errors = $this->get('Errors'))) {
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
	 * @since   3.1.0
	 */
	protected function addToolbar(): void
	{
		$canDo   = Accommodation_managerHelper::getActions();
		$toolbar = Toolbar::getInstance('toolbar');

		ToolbarHelper::title(Text::_($this->titleKey), 'generic');

		// Check if the form exists before showing the add button
		if ($this->canAdd() && $canDo->get('core.create')) {
			$toolbar->addNew($this->addTask);
		}

		if ($canDo->get('core.edit.state') || count($this->transitions)) {
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('fas fa-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();

			if (isset($this->items[0]->state)) {
				$childBar->publish($this->taskPrefix . '.publish')->listCheck(true);
				$childBar->unpublish($this->taskPrefix . '.unpublish')->listCheck(true);
				$childBar->archive($this->taskPrefix . '.archive')->listCheck(true);
			} elseif (isset($this->items[0])) {
				$toolbar->delete($this->taskPrefix . '.delete')
					->text('JTOOLBAR_EMPTY_TRASH')
					->message('JGLOBAL_CONFIRM_DELETE')
					->listCheck(true);
			}

			$childBar->standardButton('duplicate')
				->text('JTOOLBAR_DUPLICATE')
				->icon('fas fa-copy')
				->task($this->taskPrefix . '.duplicate')
				->listCheck(true);

			if (isset($this->items[0]->checked_out)) {
				$childBar->checkin($this->taskPrefix . '.checkin')->listCheck(true);
			}

			if (isset($this->items[0]->state)) {
				$childBar->trash($this->taskPrefix . '.trash')->listCheck(true);
			}
		}

		// Show delete button when viewing trashed items
		if (isset($this->items[0]->state)) {
			if ($this->state->get('filter.state') == ContentComponent::CONDITION_TRASHED && $canDo->get('core.delete')) {
				$toolbar->delete($this->taskPrefix . '.delete')
					->text('JTOOLBAR_EMPTY_TRASH')
					->message('JGLOBAL_CONFIRM_DELETE')
					->listCheck(true);
			}
		}

		if ($canDo->get('core.admin')) {
			$toolbar->preferences('com_accommodation_manager');
		}
	}

	/**
	 * Check if the add button should be shown.
	 * Override this method to add additional conditions.
	 *
	 * @return  bool
	 *
	 * @since   3.1.0
	 */
	protected function canAdd(): bool
	{
		return true;
	}

	/**
	 * Check if state is set.
	 *
	 * @param   string  $state  State key
	 *
	 * @return  mixed
	 *
	 * @since   3.1.0
	 */
	public function getState($state)
	{
		return $this->state->{$state} ?? false;
	}
}
