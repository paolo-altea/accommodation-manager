<?php
/**
 * @version    1.0.0
 * @package    Mod_Accommodation_Categories
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface
{
	public function register(Container $container): void
	{
		$container->registerServiceProvider(new ModuleDispatcherFactory('\\Accomodationmanager\\Module\\AccommodationCategories'));
		$container->registerServiceProvider(new HelperFactory('\\Accomodationmanager\\Module\\AccommodationCategories\\Site\\Helper'));
		$container->registerServiceProvider(new Module());
	}
};
