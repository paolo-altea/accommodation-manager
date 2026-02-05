<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\View\Roomsmanager;

\defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Administrator\View\BaseListView;
use Joomla\CMS\Factory;

/**
 * View class for a list of Roomsmanager.
 *
 * @since  3.1.0
 */
class HtmlView extends BaseListView
{
	protected string $taskPrefix = 'roomsmanager';
	protected string $addTask = 'roommanager.add';
	protected string $titleKey = 'COM_ACCOMMODATION_MANAGER_TITLE_ROOMSMANAGER';

	/**
	 * Flag indicating if categories exist
	 *
	 * @var    bool
	 * @since  3.1.0
	 */
	public $hasCategories = false;

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
		$this->hasCategories = $this->checkCategoriesExist();
		parent::display($tpl);
	}

	/**
	 * Check if at least one room category exists
	 *
	 * @return  bool
	 *
	 * @since   3.1.0
	 */
	protected function checkCategoriesExist(): bool
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__accommodation_manager_room_categories'))
			->where($db->quoteName('state') . ' >= 0');
		$db->setQuery($query);

		return (int) $db->loadResult() > 0;
	}

	/**
	 * Check if the add button should be shown.
	 * Requires at least one category to exist.
	 *
	 * @return  bool
	 *
	 * @since   3.1.0
	 */
	protected function canAdd(): bool
	{
		return $this->hasCategories;
	}
}
