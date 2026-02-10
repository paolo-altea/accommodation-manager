<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();

if ((int) $this->params->get('rates_load_css', 1)) {
	$wa->useStyle('com_accommodation_manager.rates-grid');
}

if ((int) $this->params->get('rates_load_js', 1)) {
	$wa->useScript('com_accommodation_manager.rates-grid');
}

$layoutPath = JPATH_SITE . '/components/com_accommodation_manager/layouts';
?>
<div class="com-accommodation-manager-rates accommodation-manager-rates">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading') ?: $this->params->get('page_title')); ?></h1>
	<?php endif; ?>

	<?php echo LayoutHelper::render('rates.grid', [
		'periods'    => $this->periods,
		'rooms'      => $this->rooms,
		'typologies' => $this->typologies,
		'grid'       => $this->grid,
		'params'     => $this->params,
	], $layoutPath); ?>
</div>
