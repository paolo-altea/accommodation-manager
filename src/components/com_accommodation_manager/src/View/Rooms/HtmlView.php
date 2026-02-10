<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Site\View\Rooms;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * View for the public rooms list.
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
	 * @var  \Joomla\Registry\Registry  Component/menu params
	 */
	protected $params;

	/**
	 * @var  array  Rate periods (loaded when rooms_show_rates is enabled)
	 */
	protected $periods = [];

	/**
	 * @var  array  Rate typologies (loaded when rooms_show_rates is enabled)
	 */
	protected $typologies = [];

	/**
	 * @var  array  Rates grid [period_id][room_id][typology_id] => rate
	 */
	protected $ratesGrid = [];

	/**
	 * Display the rooms view.
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

		// Load rates data when enabled
		if ((int) $this->params->get('rooms_show_rates', 0) && !empty($this->items))
		{
			$factory    = $app->bootComponent('com_accommodation_manager')->getMVCFactory();
			$ratesModel = $factory->createModel('Rates', 'Site');

			$hidePast = (bool) $this->params->get('rates_hide_past_periods', 0);
			$roomIds  = array_map(fn($item) => (int) $item->id, $this->items);

			$this->periods    = $ratesModel->getPeriods($hidePast);
			$this->typologies = $ratesModel->getTypologies();
			$this->ratesGrid  = $ratesModel->getRatesGrid($roomIds);
		}

		// Set page title from menu item
		$activeMenu = $app->getMenu()->getActive();

		if ($activeMenu)
		{
			$this->document->setTitle($activeMenu->title);
		}

		parent::display($tpl);
	}
}
