<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Class Accommodation_managerFrontendHelper
 *
 * @since  2.0.1
 */
class Accommodation_managerHelper
{
	/**
	 * Gets the edit permission for an user
	 *
	 * @param   mixed  $item  The item
	 *
	 * @return  bool
	 */
	public static function canUserEdit($item)
	{
		$user = Factory::getApplication()->getIdentity();

		if ($user->authorise('core.edit', 'com_accommodation_manager')) {
			return true;
		}

		if (isset($item->created_by)
			&& $user->authorise('core.edit.own', 'com_accommodation_manager')
			&& $item->created_by == $user->id
		) {
			return true;
		}

		return false;
	}
}
