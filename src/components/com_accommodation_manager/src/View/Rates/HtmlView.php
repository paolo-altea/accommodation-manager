<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Site\View\Rates;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * View for the public rates grid.
 *
 * @since  3.2.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @var  array  Rate periods
	 */
	protected $periods;

	/**
	 * @var  array  Rooms
	 */
	protected $rooms;

	/**
	 * @var  array  Rate typologies
	 */
	protected $typologies;

	/**
	 * @var  array  Rates grid [period_id][room_id][typology_id] => rate
	 */
	protected $grid;

	/**
	 * @var  \Joomla\Registry\Registry  Component/menu params
	 */
	protected $params;

	/**
	 * Display the rates view.
	 *
	 * @param   string  $tpl  The template name
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function display($tpl = null): void
	{
		$app   = Factory::getApplication();
		$model = $this->getModel();

		$this->params     = $app->getParams('com_accommodation_manager');

		$hidePast         = (bool) $this->params->get('rates_hide_past_periods', 0);
		$this->periods    = $model->getPeriods($hidePast);
		$this->rooms      = $model->getRooms();
		$this->typologies = $model->getTypologies();
		$this->grid       = $model->getRatesGrid();

		// Set page title from menu item
		$activeMenu = $app->getMenu()->getActive();

		if ($activeMenu)
		{
			$this->document->setTitle($activeMenu->title);
		}

		parent::display($tpl);
	}
}
