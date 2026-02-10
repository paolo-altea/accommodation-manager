<?php
/**
 * @version    1.0.0
 * @package    Mod_Accommodation_Rates
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Layout\LayoutHelper;

$periods    = $periods ?? [];
$rooms      = $rooms ?? [];
$typologies = $typologies ?? [];
$grid       = $grid ?? [];

$componentParams = ComponentHelper::getParams('com_accommodation_manager');

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $app->getDocument()->getWebAssetManager();
$wa->getRegistry()->addRegistryFile('media/com_accommodation_manager/joomla.asset.json');

if ((int) $params->get('load_css', 1)) {
	$wa->useStyle('com_accommodation_manager.rates-grid');
}

if ((int) $params->get('load_js', 1)) {
	$wa->useScript('com_accommodation_manager.rates-grid');
}

$layoutPath = JPATH_SITE . '/components/com_accommodation_manager/layouts';
?>
<div class="mod-accommodation-rates accommodation-manager-rates <?php echo htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8'); ?>">
	<?php echo LayoutHelper::render('rates.grid', [
		'periods'    => $periods,
		'rooms'      => $rooms,
		'typologies' => $typologies,
		'grid'       => $grid,
		'params'     => $componentParams,
	], $layoutPath); ?>
</div>
