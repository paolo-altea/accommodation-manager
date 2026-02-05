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

use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;
use Accomodationmanager\Component\Accommodation_manager\Administrator\Helper\Accommodation_managerHelper;

/**
 * Methods supporting a list of Managerroomcategories records.
 *
 * @since  2.0.1
 */
class ManagerroomcategoriesModel extends ListModel
{
	/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'ordering', 'a.ordering',
				'state', 'a.state',
				'created_by', 'a.created_by',
				'room_category_title', 'a.room_category_title',
				'room_category_parent', 'a.room_category_parent',
				'room_category_name_de', 'a.room_category_name_de',
				'room_category_name_it', 'a.room_category_name_it',
				'room_category_name_en', 'a.room_category_name_en',
				'room_category_name_fr', 'a.room_category_name_fr',
				'room_category_name_es', 'a.room_category_name_es',
				'room_category_description_de', 'a.room_category_description_de',
				'room_category_description_it', 'a.room_category_description_it',
				'room_category_description_en', 'a.room_category_description_en',
				'room_category_description_fr', 'a.room_category_description_fr',
				'room_category_description_es', 'a.room_category_description_es',
			);
		}

		parent::__construct($config);
	}


	

	

	

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState("a.id", "ASC");

		$context = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $context);

		// Split context into component and optional section
		$parts = FieldsHelper::extract($context);

		if ($parts)
		{
			$this->setState('filter.component', $parts[0]);
			$this->setState('filter.section', $parts[1]);
		}
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string A store id.
	 *
	 * @since   2.0.1
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.room_category_parent');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   2.0.1
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__accommodation_manager_room_categories` AS a');
		
		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');
		// Join over the foreign key 'room_category_parent'
		$query->select('`#__accommodation_manager_room_categories_3743785`.`room_category_title` AS managerroomcategories_fk_value_3743785');
		$query->join('LEFT', '#__accommodation_manager_room_categories AS #__accommodation_manager_room_categories_3743785 ON #__accommodation_manager_room_categories_3743785.`id` = a.`room_category_parent`');
		

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif (empty($published))
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.room_category_title LIKE ' . $search . ' )');
			}
		}

		// Filter by parent category
		$parent = $this->getState('filter.room_category_parent');

		if (is_numeric($parent) && $parent !== '')
		{
			$query->where('a.room_category_parent = ' . (int) $parent);
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', "a.id");
		$orderDirn = $this->state->get('list.direction', "ASC");

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		
		foreach ($items as $oneItem)
		{

			if (isset($oneItem->room_category_parent))
			{
				$values    = explode(',', $oneItem->room_category_parent);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = $this->getDatabase();
					$query = $db->getQuery(true);
					$query
						->select('`#__accommodation_manager_room_categories_3743785`.`room_category_title`')
						->from($db->quoteName('#__accommodation_manager_room_categories', '#__accommodation_manager_room_categories_3743785'))
						->where($db->quoteName('#__accommodation_manager_room_categories_3743785.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->room_category_title;
					}
				}

				$oneItem->room_category_parent = !empty($textValue) ? implode(', ', $textValue) : $oneItem->room_category_parent;
			}
		}

		return $items;
	}
}
