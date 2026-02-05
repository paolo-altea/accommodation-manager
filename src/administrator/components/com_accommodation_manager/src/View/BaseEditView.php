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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Base edit view class for Accommodation Manager.
 *
 * @since  3.1.0
 */
abstract class BaseEditView extends BaseHtmlView
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * The task prefix for this view (e.g., 'roommanager', 'roommanagercategory')
	 *
	 * @var    string
	 * @since  3.1.0
	 */
	protected string $taskPrefix = '';

	/**
	 * The title language key for this view
	 *
	 * @var    string
	 * @since  3.1.0
	 */
	protected string $titleKey = '';

	/**
	 * The type alias for version history (e.g., 'com_accommodation_manager.roommanager')
	 *
	 * @var    string
	 * @since  3.1.0
	 */
	protected string $typeAlias = '';

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
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');

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
		Factory::getApplication()->getInput()->set('hidemainmenu', true);

		$user       = Factory::getApplication()->getIdentity();
		$userId     = $user->get('id');
		$isNew      = ($this->item->id == 0);
		$checkedOut = isset($this->item->checked_out)
			&& $this->item->checked_out != 0
			&& $this->item->checked_out != $userId;
		$canDo      = Accommodation_managerHelper::getActions();
		$toolbar    = Toolbar::getInstance();

		// Check edit.own permission for existing items
		$canEditOwn = $canDo->get('core.edit.own')
			&& !empty($this->item->created_by)
			&& $this->item->created_by == $userId;
		$canEdit    = $canDo->get('core.edit') || $canEditOwn;

		ToolbarHelper::title(Text::_($this->titleKey), 'generic');

		// Build the save group button
		if (!$checkedOut && ($canEdit || $canDo->get('core.create'))) {
			$saveGroup = $toolbar->dropdownButton('save-group');

			$saveGroup->configure(
				function (Toolbar $childBar) use ($canDo, $isNew) {
					$childBar->apply($this->taskPrefix . '.apply');
					$childBar->save($this->taskPrefix . '.save');

					if ($canDo->get('core.create')) {
						$childBar->save2new($this->taskPrefix . '.save2new');
					}

					if (!$isNew && $canDo->get('core.create')) {
						$childBar->save2copy($this->taskPrefix . '.save2copy');
					}
				}
			);
		}

		// Button for version control
		if ($this->state->params->get('save_history', 1) && $user->authorise('core.edit') && !$isNew) {
			ToolbarHelper::versions($this->typeAlias, $this->item->id);
		}

		// Cancel/Close button
		$toolbar->cancel(
			$this->taskPrefix . '.cancel',
			$isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'
		);
	}
}
