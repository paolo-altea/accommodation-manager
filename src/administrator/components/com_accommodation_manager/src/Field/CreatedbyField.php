<?php
/**
 * @version    CVS: 2.0.1
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2019. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\Field;

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\User\UserFactoryInterface;

/**
 * Supports an HTML select list of categories
 *
 * @since  2.0.1
 */
class CreatedbyField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    tring
	 * @since  2.0.1
	 */
	protected $type = 'createdby';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string    The field input markup.
	 *
	 * @since   2.0.1
	 */
	protected function getInput()
	{
		$html = [];

		$user_id = $this->value;

		if ($user_id) {
			$user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById((int) $user_id);
		} else {
			$user   = Factory::getApplication()->getIdentity();
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $user->id . '" />';
		}

		if (!$this->hidden) {
			$html[] = '<div>' . htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8')
				. ' (' . htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8') . ')</div>';
		}

		return implode($html);
	}
}
