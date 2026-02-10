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

use Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper;
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
		$roomIds   = !empty($this->items) ? array_map(fn($item) => (int) $item->id, $this->items) : [];
		$ratesData = Accommodation_managerHelper::loadRatesData($this->params, $roomIds);

		if ($ratesData)
		{
			$this->periods    = $ratesData['periods'];
			$this->typologies = $ratesData['typologies'];
			$this->ratesGrid  = $ratesData['ratesGrid'];
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
