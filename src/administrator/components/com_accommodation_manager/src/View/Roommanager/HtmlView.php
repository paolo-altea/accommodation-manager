<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\View\Roommanager;

\defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Administrator\View\BaseEditView;
use Joomla\CMS\Factory;

/**
 * View class for a single Roommanager.
 *
 * @since  3.1.0
 */
class HtmlView extends BaseEditView
{
	protected string $taskPrefix = 'roommanager';
	protected string $titleKey = 'COM_ACCOMMODATION_MANAGER_TITLE_ROOMMANAGER';
	protected string $typeAlias = 'com_accommodation_manager.roommanager';

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
}
