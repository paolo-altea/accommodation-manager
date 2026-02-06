<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Site\View\Categories;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * View for the public room categories list.
 *
 * @since  3.2.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @var  array  List of category items
	 */
	protected $items;

	/**
	 * @var  \Joomla\Registry\Registry  Component/menu params
	 */
	protected $params;

	/**
	 * Display the categories view.
	 *
	 * @param   string  $tpl  The template name
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function display($tpl = null): void
	{
		$app = Factory::getApplication();

		$this->items  = $this->get('Items');
		$this->params = $app->getParams('com_accommodation_manager');

		// Set page title from menu item
		$activeMenu = $app->getMenu()->getActive();

		if ($activeMenu)
		{
			$this->document->setTitle($activeMenu->title);
		}

		parent::display($tpl);
	}
}
