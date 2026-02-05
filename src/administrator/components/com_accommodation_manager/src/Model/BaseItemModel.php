<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;

/**
 * Base item model class for Accommodation Manager.
 * Provides common functionality for single-item CRUD operations.
 *
 * @since  3.1.0
 */
abstract class BaseItemModel extends AdminModel
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  3.1.0
	 */
	protected $text_prefix = 'COM_ACCOMMODATION_MANAGER';

	/**
	 * Item data cache.
	 *
	 * @var    object|null
	 * @since  3.1.0
	 */
	protected $item = null;

	/**
	 * The database table name for ordering queries.
	 * Must be set by child classes (e.g., '#__accommodation_manager_rooms').
	 *
	 * @var    string
	 * @since  3.1.0
	 */
	protected string $tableName = '';

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interrogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|false  A Form object on success, false on failure
	 *
	 * @since   3.1.0
	 */
	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm(
			'com_accommodation_manager.' . $this->name,
			$this->name,
			[
				'control'   => 'jform',
				'load_data' => $loadData,
			]
		);

		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   3.1.0
	 */
	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState(
			'com_accommodation_manager.edit.' . $this->name . '.data',
			[]
		);

		if (empty($data)) {
			if ($this->item === null) {
				$this->item = $this->getItem();
			}

			$data = $this->item;
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  object|false  Object on success, false on failure.
	 *
	 * @since   3.1.0
	 */
	public function getItem($pk = null)
	{
		return parent::getItem($pk);
	}

	/**
	 * Method to duplicate items.
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  \Exception
	 * @since   3.1.0
	 */
	public function duplicate(&$pks): bool
	{
		$app  = Factory::getApplication();
		$user = $app->getIdentity();

		if (!$user->authorise('core.create', 'com_accommodation_manager')) {
			throw new \Exception(Text::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$context = $this->option . '.' . $this->name;

		PluginHelper::importPlugin($this->events_map['save']);

		$table = $this->getTable();

		foreach ($pks as $pk) {
			$table->load($pk, true);
			$table->id = 0;
			$table->check();

			// Allow child classes to modify the table before storing
			$this->prepareDuplicate($table);

			$result = $app->triggerEvent($this->event_before_save, [$context, &$table, true, $table]);

			if (in_array(false, $result, true)) {
				throw new \Exception(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'));
			}

			$table->store();

			$app->triggerEvent($this->event_after_save, [$context, &$table, true]);
		}

		$this->cleanCache();

		return true;
	}

	/**
	 * Hook method for child classes to modify table data before duplicating.
	 * Override this method to handle entity-specific fields.
	 *
	 * @param   Table  $table  The table object being duplicated
	 *
	 * @return  void
	 *
	 * @since   3.1.0
	 */
	protected function prepareDuplicate(Table $table): void
	{
		// Default implementation does nothing.
		// Override in child classes for entity-specific logic.
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   Table  $table  Table Object
	 *
	 * @return  void
	 *
	 * @since   3.1.0
	 */
	protected function prepareTable($table): void
	{
		if (empty($table->id) && empty($this->tableName)) {
			return;
		}

		if (empty($table->id)) {
			if (!isset($table->ordering) || $table->ordering === '') {
				$db    = $this->getDatabase();
				$query = $db->getQuery(true)
					->select('MAX(' . $db->quoteName('ordering') . ')')
					->from($db->quoteName($this->tableName));
				$db->setQuery($query);
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}
}
