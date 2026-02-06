<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Display controller for the public frontend.
 *
 * @since  3.2.0
 */
class DisplayController extends BaseController
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached.
	 * @param   array    $urlparams  An array of safe URL parameters.
	 *
	 * @return  BaseController  This object to support chaining.
	 *
	 * @since   3.2.0
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$view = $this->input->getCmd('view', 'categories');
		$this->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
}
