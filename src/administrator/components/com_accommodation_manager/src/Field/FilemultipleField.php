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

use \Joomla\CMS\Form\FormField;

/**
 * Supports an HTML select list of categories
 *
 * @since  2.0.1
 */
class FileMultipleField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  2.0.1
	 */
	protected $type = 'filemultiple';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string    The field input markup.
	 *
	 * @since   2.0.1
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = '<input type="file" name="' . $this->name . '[]" multiple>';

		return $html;
	}
}
