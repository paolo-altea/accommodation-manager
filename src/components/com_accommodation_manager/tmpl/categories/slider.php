<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// Global display params
$showImage       = $this->params->get('categories_show_image', 1);
$showDescription = $this->params->get('categories_show_description', 1);
$showLinkButton  = $this->params->get('categories_show_link_button', 0);

// Swiper params from component config (Categories tab)
$slidesPerViewMobile  = (float) $this->params->get('categories_slides_per_view_mobile', 1);
$slidesPerViewDesktop = (float) $this->params->get('categories_slides_per_view_desktop', 1);
$spaceBetweenMobile   = (int) $this->params->get('categories_space_between_mobile', 10);
$spaceBetweenDesktop  = (int) $this->params->get('categories_space_between_desktop', 30);
$autoplay             = (int) $this->params->get('categories_swiper_autoplay', 0);
$autoplayDelay        = (int) $this->params->get('categories_swiper_autoplay_delay', 5000);
$showNavigation       = (int) $this->params->get('categories_swiper_navigation', 1);
$showPagination       = (int) $this->params->get('categories_swiper_pagination', 1);
$loadCss              = (int) $this->params->get('categories_swiper_load_css', 1);
$loadJs               = (int) $this->params->get('categories_swiper_load_js', 1);

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();

if ($loadJs) {
	$wa->useScript('com_accommodation_manager.categories-slider');
}

if ($loadCss) {
	$wa->useStyle('com_accommodation_manager.categories-slider');
}
?>
<div class="com-accommodation-manager-categories">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading') ?: $this->params->get('page_title')); ?></h1>
	<?php endif; ?>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-info">
			<?php echo Text::_('COM_ACCOMMODATION_MANAGER_NO_CATEGORIES'); ?>
		</div>
	<?php else : ?>
		<div class="am-categories-swiper swiper"
			 data-slides-per-view-mobile="<?php echo $this->escape($slidesPerViewMobile); ?>"
			 data-slides-per-view-desktop="<?php echo $this->escape($slidesPerViewDesktop); ?>"
			 data-space-between-mobile="<?php echo $spaceBetweenMobile; ?>"
			 data-space-between-desktop="<?php echo $spaceBetweenDesktop; ?>"
			 data-autoplay="<?php echo $autoplay ? $autoplayDelay : '0'; ?>"
			 data-navigation="<?php echo $showNavigation; ?>"
			 data-pagination="<?php echo $showPagination; ?>">
			<div class="swiper-wrapper">
				<?php foreach ($this->items as $item) :
					$categoryUrl = Route::_('index.php?option=com_accommodation_manager&view=category&id=' . (int) $item->id);
				?>
					<div class="swiper-slide">
						<?php if ($showImage && !empty($item->image)) :
							$imgData   = HTMLHelper::_('cleanImageURL', $item->image);
							$imgAttribs = ['class' => 'am-slide-img', 'loading' => 'lazy'];
							if (!empty($imgData->attributes['width'])) {
								$imgAttribs['width'] = (int) $imgData->attributes['width'];
							}
							if (!empty($imgData->attributes['height'])) {
								$imgAttribs['height'] = (int) $imgData->attributes['height'];
							}
							echo HTMLHelper::_('image', $imgData->url, $item->image_alt ?? '', $imgAttribs);
						endif; ?>
						<div class="am-slide-content">
							<h2 class="am-slide-title">
								<a href="<?php echo $categoryUrl; ?>">
									<?php echo htmlspecialchars($item->name ?? '', ENT_QUOTES, 'UTF-8'); ?>
								</a>
							</h2>
							<?php if ($showDescription && !empty($item->description)) : ?>
								<div class="am-slide-description">
									<?php echo $item->description; ?>
								</div>
							<?php endif; ?>
							<?php if ($showLinkButton) : ?>
								<a class="btn btn-primary am-slide-btn" href="<?php echo $categoryUrl; ?>">
									<?php echo Text::_('COM_ACCOMMODATION_MANAGER_VIEW_ROOMS'); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ($showNavigation) : ?>
				<div class="swiper-button-prev"></div>
				<div class="swiper-button-next"></div>
			<?php endif; ?>

			<?php if ($showPagination) : ?>
				<div class="swiper-pagination"></div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
