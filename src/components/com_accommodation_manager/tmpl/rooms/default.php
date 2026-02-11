<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

// Show/hide toggles
$showSurface     = $this->params->get('rooms_show_surface', 1);
$showPeople      = $this->params->get('rooms_show_people', 1);
$showPriceFrom   = $this->params->get('rooms_show_price_from', 1);
$showIntro       = $this->params->get('rooms_show_intro', 1);
$showDescription = $this->params->get('rooms_show_description', 1);
$showFloorPlan   = $this->params->get('rooms_show_floor_plan', 1);
$showGallery     = $this->params->get('rooms_show_gallery', 1);
$showVideo       = $this->params->get('rooms_show_video', 1);
$showRates       = (int) $this->params->get('rooms_show_rates', 0);
$showInfo        = $showSurface || $showPeople || $showPriceFrom;

// Rates grid assets
if ($showRates && !empty($this->periods))
{
	/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
	$wa = $this->document->getWebAssetManager();

	if ((int) $this->params->get('rates_load_css', 1)) {
		$wa->useStyle('com_accommodation_manager.rates-grid');
	}
	if ((int) $this->params->get('rates_load_js', 1)) {
		$wa->useScript('com_accommodation_manager.rates-grid');
	}
}

$layoutPath = JPATH_SITE . '/components/com_accommodation_manager/layouts';

// Gallery Swiper
$gallerySwiper = $showGallery && $this->params->get('rooms_enable_swiper', 0);
$gallerySwiperConfig = [];

if ($gallerySwiper)
{
	$gallerySwiperConfig = [
		'slidesPerViewMobile'  => (float) $this->params->get('rooms_gallery_slides_per_view_mobile', 1),
		'slidesPerViewDesktop' => (float) $this->params->get('rooms_gallery_slides_per_view_desktop', 1),
		'spaceBetweenMobile'   => (int) $this->params->get('rooms_gallery_space_between_mobile', 10),
		'spaceBetweenDesktop'  => (int) $this->params->get('rooms_gallery_space_between_desktop', 30),
		'autoplay'             => (int) $this->params->get('rooms_gallery_autoplay', 0),
		'autoplayDelay'        => (int) $this->params->get('rooms_gallery_autoplay_delay', 5000),
		'navigation'           => (int) $this->params->get('rooms_gallery_navigation', 1),
		'pagination'           => (int) $this->params->get('rooms_gallery_pagination', 1),
	];

	/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
	$wa = $this->document->getWebAssetManager();

	if ((int) $this->params->get('rooms_gallery_load_js', 1)) {
		$wa->useScript('com_accommodation_manager.gallery-slider');
	}
	if ((int) $this->params->get('rooms_gallery_load_css', 1)) {
		$wa->useStyle('com_accommodation_manager.gallery-slider');
	}
}

// Request/booking buttons
$showRequestBtn = $this->params->get('rooms_show_request_button', 1);
$showBookingBtn = $this->params->get('rooms_show_booking_button', 1);
$lang           = Accommodation_managerHelper::getLanguageSuffix();
$requestUrl     = $this->params->get('request_link_' . $lang, '');
$bookingUrl     = $this->params->get('booking_link_' . $lang, '');

// Detail link
$showDetailLink = $this->params->get('rooms_show_detail_link', 0);

// Category filter
$showCategoryFilter = $this->params->get('rooms_show_category_filter', 0);

if ($showCategoryFilter && !empty($this->items))
{
	// Extract unique categories preserving order
	$filterCategories = [];

	foreach ($this->items as $item)
	{
		$catId = (int) $item->room_category;

		if (!isset($filterCategories[$catId]) && !empty($item->category_name))
		{
			$filterCategories[$catId] = $item->category_name;
		}
	}

	// Only show filter if there are 2+ categories
	if (count($filterCategories) < 2)
	{
		$showCategoryFilter = false;
	}
	else
	{
		$this->document->getWebAssetManager()->useScript('com_accommodation_manager.category-filter');
	}
}
?>
<div class="com-accommodation-manager-rooms">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading') ?: $this->params->get('page_title')); ?></h1>
	<?php endif; ?>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-info">
			<?php echo Text::_('COM_ACCOMMODATION_MANAGER_NO_ROOMS'); ?>
		</div>
	<?php else : ?>
		<?php if ($showCategoryFilter) : ?>
			<div class="rooms-category-filter">
				<button type="button" class="btn btn-outline-primary active" data-filter-category=""><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FILTER_ALL'); ?></button>
				<?php foreach ($filterCategories as $catId => $catName) : ?>
					<button type="button" class="btn btn-outline-primary" data-filter-category="<?php echo (int) $catId; ?>"><?php echo htmlspecialchars($catName, ENT_QUOTES, 'UTF-8'); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php foreach ($this->items as $item) : ?>
		<section class="room-item" id="room-<?php echo htmlspecialchars($item->room_name, ENT_QUOTES, 'UTF-8'); ?>" data-category="<?php echo (int) $item->room_category; ?>">

			<?php if (!empty($item->title)) : ?>
				<h2 class="room-title"><?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?></h2>
			<?php endif; ?>

			<?php if (!empty($item->category_name)) : ?>
				<p class="room-category"><?php echo htmlspecialchars($item->category_name, ENT_QUOTES, 'UTF-8'); ?></p>
			<?php endif; ?>

			<?php // Thumbnail ?>
			<?php if (!empty($item->thumbnail)) :
				$thumbData    = HTMLHelper::_('cleanImageURL', $item->thumbnail);
				$thumbAttribs = ['loading' => 'lazy'];
				if (!empty($thumbData->attributes['width'])) {
					$thumbAttribs['width'] = (int) $thumbData->attributes['width'];
				}
				if (!empty($thumbData->attributes['height'])) {
					$thumbAttribs['height'] = (int) $thumbData->attributes['height'];
				}
			?>
				<div class="room-thumbnail">
					<?php echo HTMLHelper::_('image', $thumbData->url, $item->thumbnail_alt ?? '', $thumbAttribs); ?>
				</div>
			<?php endif; ?>

			<?php // Basic info ?>
			<?php if ($showInfo) : ?>
				<div class="room-info">
					<?php if ($showSurface && !empty($item->room_surface)) : ?>
						<span class="room-surface">
							<?php echo Text::_('COM_ACCOMMODATION_MANAGER_ROOM_SURFACE'); ?>:
							<?php echo htmlspecialchars($item->room_surface, ENT_QUOTES, 'UTF-8'); ?>
						</span>
					<?php endif; ?>

					<?php if ($showPeople && !empty($item->room_people)) : ?>
						<span class="room-people">
							<?php echo Text::_('COM_ACCOMMODATION_MANAGER_ROOM_PEOPLE'); ?>:
							<?php echo htmlspecialchars($item->room_people, ENT_QUOTES, 'UTF-8'); ?>
						</span>
					<?php endif; ?>

					<?php if ($showPriceFrom && !empty($item->room_price_from)) : ?>
						<span class="room-price-from">
							<?php echo Text::_('COM_ACCOMMODATION_MANAGER_ROOM_PRICE_FROM'); ?>:
							<?php echo htmlspecialchars($item->room_price_from, ENT_QUOTES, 'UTF-8'); ?>
						</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php // Intro ?>
			<?php if ($showIntro && !empty($item->intro)) : ?>
				<div class="room-intro">
					<?php echo $item->intro; ?>
				</div>
			<?php endif; ?>

			<?php // Description ?>
			<?php if ($showDescription && !empty($item->description)) : ?>
				<div class="room-description">
					<?php echo $item->description; ?>
				</div>
			<?php endif; ?>

			<?php // Detail link ?>
			<?php if ($showDetailLink) : ?>
				<div class="room-detail-link">
					<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_accommodation_manager&view=room&id=' . (int) $item->id); ?>">
						<?php echo Text::_('COM_ACCOMMODATION_MANAGER_ROOM_DETAIL'); ?>
					</a>
				</div>
			<?php endif; ?>

			<?php // Floor plan ?>
			<?php if ($showFloorPlan && !empty($item->floor_plan)) :
				$fpData    = HTMLHelper::_('cleanImageURL', $item->floor_plan);
				$fpAttribs = ['loading' => 'lazy'];
				if (!empty($fpData->attributes['width'])) {
					$fpAttribs['width'] = (int) $fpData->attributes['width'];
				}
				if (!empty($fpData->attributes['height'])) {
					$fpAttribs['height'] = (int) $fpData->attributes['height'];
				}
			?>
				<div class="room-floor-plan">
					<h3><?php echo Text::_('COM_ACCOMMODATION_MANAGER_ROOM_FLOOR_PLAN'); ?></h3>
					<?php echo HTMLHelper::_('image', $fpData->url, $item->floor_plan_alt ?? '', $fpAttribs); ?>
				</div>
			<?php endif; ?>

			<?php // Gallery ?>
			<?php if ($showGallery && !empty($item->gallery_items)) : ?>
				<?php echo LayoutHelper::render('room.gallery', [
					'items'        => $item->gallery_items,
					'swiper'       => $gallerySwiper,
					'swiperConfig' => $gallerySwiperConfig,
				], $layoutPath); ?>
			<?php endif; ?>

			<?php // Video ?>
			<?php if ($showVideo && !empty($item->video)) : ?>
				<div class="room-video">
					<?php echo $item->video; ?>
				</div>
			<?php endif; ?>

			<?php // Rates grid ?>
			<?php if ($showRates && !empty($this->periods)) : ?>
				<div class="room-rates accommodation-manager-rates">
					<?php echo LayoutHelper::render('rates.room-grid', [
						'periods'    => $this->periods,
						'typologies' => $this->typologies,
						'grid'       => $this->ratesGrid,
						'roomId'     => (int) $item->id,
						'params'     => $this->params,
					], $layoutPath); ?>
				</div>
			<?php endif; ?>

			<?php // Request / Booking buttons ?>
			<?php if (($showRequestBtn && $requestUrl) || ($showBookingBtn && $bookingUrl)) : ?>
				<div class="room-actions">
					<?php if ($showRequestBtn && $requestUrl) : ?>
						<a class="btn btn-primary room-request-btn" href="<?php echo htmlspecialchars($requestUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
							<?php echo Text::_('COM_ACCOMMODATION_MANAGER_REQUEST'); ?>
						</a>
					<?php endif; ?>
					<?php if ($showBookingBtn && $bookingUrl) : ?>
						<a class="btn btn-primary room-booking-btn" href="<?php echo htmlspecialchars($bookingUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
							<?php echo Text::_('COM_ACCOMMODATION_MANAGER_BOOKING'); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</section>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
