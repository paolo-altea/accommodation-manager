<?php
/**
 * @version    CVS: 2.0.1
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2019. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Accomodationmanager\Component\Accommodation_manager\Administrator\Extension\Accommodation_managerComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;


/**
 * The Accommodation_manager service provider.
 *
 * @since  2.0.1
 */
return new class implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   2.0.1
	 */
	public function register(Container $container)
	{

		$container->registerServiceProvider(new CategoryFactory('\\Accomodationmanager\\Component\\Accommodation_manager'));
		$container->registerServiceProvider(new MVCFactory('\\Accomodationmanager\\Component\\Accommodation_manager'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\Accomodationmanager\\Component\\Accommodation_manager'));
		$container->registerServiceProvider(new RouterFactory('\\Accomodationmanager\\Component\\Accommodation_manager'));

		$container->set(
			ComponentInterface::class,
			function (Container $container)
			{
				$component = new Accommodation_managerComponent($container->get(ComponentDispatcherFactoryInterface::class));

				$component->setRegistry($container->get(Registry::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setCategoryFactory($container->get(CategoryFactoryInterface::class));
				$component->setRouterFactory($container->get(RouterFactoryInterface::class));

				return $component;
			}
		);
	}
};
