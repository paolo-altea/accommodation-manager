<?php
/**
 * @version    1.0.0
 * @package    Mod_Accommodation_Categories
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Module\AccommodationCategories\Site\Helper;

defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Registry\Registry;

class CategoriesHelper implements DatabaseAwareInterface
{
	use DatabaseAwareTrait;

	/**
	 * Get categories from the database.
	 *
	 * @param   Registry  $params  Module parameters
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function getCategories(Registry $params): array
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
			->where($db->quoteName('a.state') . ' = 1');

		// Ordering
		$orderCol = $params->get('ordering', 'a.ordering');
		$orderDir = strtoupper($params->get('ordering_direction', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

		$allowedColumns = ['a.ordering', 'a.room_category_title', 'name'];

		if (!in_array($orderCol, $allowedColumns, true))
		{
			$orderCol = 'a.ordering';
		}

		if ($orderCol === 'name')
		{
			$orderCol = 'a.room_category_name_' . $lang;
		}

		$query->order($db->quoteName($orderCol) . ' ' . $orderDir);

		// Limit
		$count = (int) $params->get('count', 0);

		if ($count > 0)
		{
			$query->setLimit($count);
		}

		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
