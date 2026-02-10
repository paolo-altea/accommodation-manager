<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Site\View\Category;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * View for rooms filtered by a single category.
 *
 * @since  3.2.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @var  array  List of room items
	 */
	protected $items;

	/**
	 * @var  object|null  Category data
	 */
	protected $category;

	/**
	 * @var  \Joomla\Registry\Registry  Component/menu params
	 */
	protected $params;

	/**
	 * Display the category rooms view.
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

		$this->items    = $this->get('Items');
		$this->category = $this->get('Category');
		$this->params   = $app->getParams('com_accommodation_manager');

		// Set page title: category name or menu item title
		$activeMenu = $app->getMenu()->getActive();

		if (!empty($this->category->name))
		{
			$this->document->setTitle($this->category->name);
		}
		elseif ($activeMenu)
		{
			$this->document->setTitle($activeMenu->title);
		}

		parent::display($tpl);
	}
}
