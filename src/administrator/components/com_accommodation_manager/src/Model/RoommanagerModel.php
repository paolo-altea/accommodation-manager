<?php
/**
 * @version    CVS: 2.0.1
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2019. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\Table\Table;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Plugin\PluginHelper;
use \Joomla\CMS\MVC\Model\AdminModel;
use \Joomla\CMS\Helper\TagsHelper;

/**
 * Roommanager model.
 *
 * @since  2.0.1
 */
class RoommanagerModel extends AdminModel
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 *
	 * @since  2.0.1
	 */
	protected $text_prefix = 'COM_ACCOMMODATION_MANAGER';

	/**
	 * @var    string  Alias to manage history control
	 *
	 * @since  2.0.1
	 */
	public $typeAlias = 'com_accommodation_manager.roommanager';

	/**
	 * @var    null  Item data
	 *
	 * @since  2.0.1
	 */
	protected $item = null;

	
	

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table    A database object
	 *
	 * @since   2.0.1
	 */
	public function getTable($type = 'Roommanager', $prefix = 'Administrator', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  \JForm|boolean  A \JForm object on success, false on failure
	 *
	 * @since   2.0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = Factory::getApplication();

		// Get the form.
		$form = $this->loadForm(
								'com_accommodation_manager.roommanager', 
								'roommanager',
								array(
									'control' => 'jform',
									'load_data' => $loadData 
								)
							);

		

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   2.0.1
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_accommodation_manager.edit.roommanager.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
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
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   2.0.1
	 */
	public function getItem($pk = null)
	{
		
			if ($item = parent::getItem($pk))
			{
				if (isset($item->params))
				{
					$item->params = json_encode($item->params);
				}
				
				// Do any procesing on fields here if needed
			}

			return $item;
		
	}

	/**
	 * Method to duplicate an Roommanager
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		$app = Factory::getApplication();
		$user = Factory::getApplication()->getIdentity();

		// Access checks.
		if (!$user->authorise('core.create', 'com_accommodation_manager'))
		{
			throw new \Exception(Text::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$context = $this->option . '.' . $this->name;

		// Include the plugins for the save events.
		PluginHelper::importPlugin($this->events_map['save']);

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			try
			{
				$table->load($pk, true);

				// Reset the id to create a new record.
				$table->id = 0;

				$table->check();

				if (!empty($table->room_category))
				{
					if (is_array($table->room_category))
					{
						$table->room_category = implode(',', $table->room_category);
					}
				}
				else
				{
					$table->room_category = '';
				}

				// Trigger the before save event.
				$result = $app->triggerEvent($this->event_before_save, array($context, &$table, true, $table));

				if (in_array(false, $result, true))
				{
					throw new \Exception(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'));
				}

				$table->store();

				// Trigger the after save event.
				$app->triggerEvent($this->event_after_save, array($context, &$table, true));
			}
			catch (\Exception $e)
			{
				throw new \Exception($e->getMessage());
			}
		}

		// Clean cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   Table  $table  Table Object
	 *
	 * @return  void
	 *
	 * @since   2.0.1
	 */
	protected function prepareTable($table)
	{
		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = $this->getDatabase();
				$db->setQuery('SELECT MAX(ordering) FROM #__accommodation_manager_rooms');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}
}
