<?php
/**
 * @version    1.0.0
 * @package    Mod_Accommodation_Rooms
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

/** @var array $items */
$items = $items ?? [];

if (empty($items))
{
	return;
}

// Show/hide toggles
$showSurface     = (int) $params->get('show_surface', 1);
$showPeople      = (int) $params->get('show_people', 1);
$showPriceFrom   = (int) $params->get('show_price_from', 1);
$showIntro       = (int) $params->get('show_intro', 1);
$showDescription = (int) $params->get('show_description', 1);
$showFloorPlan   = (int) $params->get('show_floor_plan', 1);
$showGallery     = (int) $params->get('show_gallery', 1);
$showVideo       = (int) $params->get('show_video', 1);
$showInfo        = $showSurface || $showPeople || $showPriceFrom;

// Gallery Swiper
$gallerySwiper = $showGallery && (int) $params->get('enable_swiper', 0);
$gallerySwiperConfig = [];

if ($gallerySwiper)
{
	$gallerySwiperConfig = [
		'slidesPerViewMobile'  => (float) $params->get('gallery_slides_per_view_mobile', 1),
		'slidesPerViewDesktop' => (float) $params->get('gallery_slides_per_view_desktop', 1),
		'spaceBetweenMobile'   => (int) $params->get('gallery_space_between_mobile', 10),
		'spaceBetweenDesktop'  => (int) $params->get('gallery_space_between_desktop', 30),
		'autoplay'             => (int) $params->get('gallery_autoplay', 0),
		'autoplayDelay'        => (int) $params->get('gallery_autoplay_delay', 5000),
		'navigation'           => (int) $params->get('gallery_navigation', 1),
		'pagination'           => (int) $params->get('gallery_pagination', 1),
	];

	/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
	$wa = $app->getDocument()->getWebAssetManager();
	$wa->getRegistry()->addRegistryFile('media/com_accommodation_manager/joomla.asset.json');

	if ((int) $params->get('load_js', 1)) {
		$wa->useScript('com_accommodation_manager.gallery-slider');
	}
	if ((int) $params->get('load_css', 1)) {
		$wa->useStyle('com_accommodation_manager.gallery-slider');
	}
}

// Layout path for shared layouts
$layoutPath = JPATH_SITE . '/components/com_accommodation_manager/layouts';

// Request/booking buttons
$showRequestBtn = (int) $params->get('show_request_button', 1);
$showBookingBtn = (int) $params->get('show_booking_button', 1);
$lang           = Accommodation_managerHelper::getLanguageSuffix();
$componentParams = ComponentHelper::getParams('com_accommodation_manager');
$requestUrl     = $componentParams->get('request_link_' . $lang, '');
$bookingUrl     = $componentParams->get('booking_link_' . $lang, '');

// Detail link
$showDetailLink = (int) $params->get('show_detail_link', 0);
?>
<div class="mod-accommodation-rooms <?php echo htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8'); ?>">
	<?php foreach ($items as $item) : ?>
	<section class="room-item" id="mod-room-<?php echo htmlspecialchars($item->room_name, ENT_QUOTES, 'UTF-8'); ?>">

		<?php if (!empty($item->title)) : ?>
			<h3 class="room-title"><?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?></h3>
		<?php endif; ?>

		<?php if (!empty($item->category_name)) : ?>
			<p class="room-category"><?php echo htmlspecialchars($item->category_name, ENT_QUOTES, 'UTF-8'); ?></p>
		<?php endif; ?>

		<?php // Thumbnail ?>
		<?php echo LayoutHelper::render('room.thumbnail', [
			'src' => $item->thumbnail ?? '',
			'alt' => $item->thumbnail_alt ?? '',
		], $layoutPath); ?>

		<?php // Basic info ?>
		<?php echo LayoutHelper::render('room.info', [
			'surface'    => $item->room_surface ?? '',
			'people'     => $item->room_people ?? '',
			'price_from' => $item->room_price_from ?? '',
			'show'       => ['surface' => $showSurface, 'people' => $showPeople, 'price_from' => $showPriceFrom],
			'langPrefix' => 'MOD_ACCOMMODATION_ROOMS',
		], $layoutPath); ?>

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
		<?php if ($showDetailLink) :
			echo LayoutHelper::render('room.detail-link', [
				'roomId'     => (int) $item->id,
				'langPrefix' => 'MOD_ACCOMMODATION_ROOMS',
			], $layoutPath);
		endif; ?>

		<?php // Floor plan ?>
		<?php if ($showFloorPlan) :
			echo LayoutHelper::render('room.floor-plan', [
				'src'        => $item->floor_plan ?? '',
				'alt'        => $item->floor_plan_alt ?? '',
				'headingTag' => 'h4',
				'langPrefix' => 'MOD_ACCOMMODATION_ROOMS',
			], $layoutPath);
		endif; ?>

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

		<?php // Request / Booking buttons ?>
		<?php echo LayoutHelper::render('room.actions', [
			'requestUrl'     => $requestUrl,
			'bookingUrl'     => $bookingUrl,
			'showRequestBtn' => $showRequestBtn,
			'showBookingBtn' => $showBookingBtn,
			'langPrefix'     => 'MOD_ACCOMMODATION_ROOMS',
		], $layoutPath); ?>

	</section>
	<?php endforeach; ?>
</div>
