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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

// Show/hide toggles
$showSurface     = $this->params->get('rooms_show_surface', 1);
$showPeople      = $this->params->get('rooms_show_people', 1);
$showPriceFrom   = $this->params->get('rooms_show_price_from', 1);
$showIntro       = $this->params->get('rooms_show_intro', 1);
$showDescription = $this->params->get('rooms_show_description', 1);
$showFloorPlan   = $this->params->get('rooms_show_floor_plan', 1);
$showGallery     = $this->params->get('rooms_show_gallery', 1);
$showVideo       = $this->params->get('rooms_show_video', 1);
$showInfo        = $showSurface || $showPeople || $showPriceFrom;

// Detail link
$showDetailLink = $this->params->get('rooms_show_detail_link', 0);

// Category description
$showCategoryDesc = $this->params->get('rooms_show_category_description', 0);

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

// Group items by category
$groupedItems = [];

if (!empty($this->items))
{
	foreach ($this->items as $item)
	{
		$groupedItems[(int) $item->room_category][] = $item;
	}
}
?>
<div class="com-accommodation-manager-rooms com-accommodation-manager-rooms--grouped">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading') ?: $this->params->get('page_title')); ?></h1>
	<?php endif; ?>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-info">
			<?php echo Text::_('COM_ACCOMMODATION_MANAGER_NO_ROOMS'); ?>
		</div>
	<?php else : ?>
		<?php foreach ($groupedItems as $categoryId => $items) : ?>
			<div class="rooms-category-group" data-category="<?php echo $categoryId; ?>">
				<?php if (!empty($items[0]->category_name)) : ?>
					<h2 class="rooms-category-heading"><?php echo htmlspecialchars($items[0]->category_name, ENT_QUOTES, 'UTF-8'); ?></h2>
				<?php endif; ?>

				<?php if ($showCategoryDesc && !empty($items[0]->category_description)) : ?>
					<div class="rooms-category-description">
						<?php echo $items[0]->category_description; ?>
					</div>
				<?php endif; ?>

				<div class="rooms-category-items">
					<?php foreach ($items as $item) : ?>
					<section class="room-item" id="room-<?php echo htmlspecialchars($item->room_name, ENT_QUOTES, 'UTF-8'); ?>">

						<?php if (!empty($item->title)) : ?>
							<h3 class="room-title"><?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?></h3>
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
							'langPrefix' => 'COM_ACCOMMODATION_MANAGER',
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
								'langPrefix' => 'COM_ACCOMMODATION_MANAGER',
							], $layoutPath);
						endif; ?>

						<?php // Floor plan ?>
						<?php if ($showFloorPlan) :
							echo LayoutHelper::render('room.floor-plan', [
								'src'        => $item->floor_plan ?? '',
								'alt'        => $item->floor_plan_alt ?? '',
								'headingTag' => 'h4',
								'langPrefix' => 'COM_ACCOMMODATION_MANAGER',
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
							'langPrefix'     => 'COM_ACCOMMODATION_MANAGER',
						], $layoutPath); ?>

					</section>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
