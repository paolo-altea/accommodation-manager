<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Site\Model;

defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper;
use Joomla\CMS\MVC\Model\ListModel;

/**
 * Categories list model for the public frontend.
 *
 * @since  3.2.0
 */
class CategoriesModel extends ListModel
{
	/**
	 * Build the query to retrieve published room categories.
	 *
	 * @return  \Joomla\Database\QueryInterface
	 *
	 * @since   3.2.0
	 */
	protected function getListQuery()
	{
		$db   = $this->getDatabase();
		$lang = Accommodation_managerHelper::getLanguageSuffix();

		$query = $db->getQuery(true)
			->select([
				$db->quoteName('a.id'),
				$db->quoteName('a.room_category_name_' . $lang, 'name'),
				$db->quoteName('a.room_category_description_' . $lang, 'description'),
				$db->quoteName('a.room_category_image', 'image'),
				$db->quoteName('a.room_category_image_alt_' . $lang, 'image_alt'),
				$db->quoteName('a.ordering'),
			])
			->from($db->quoteName('#__accommodation_manager_room_categories', 'a'))
			->where($db->quoteName('a.state') . ' = 1')
			->order($db->quoteName('a.ordering') . ' ASC');

		return $query;
	}
}
