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

$item = $this->item;

// Gallery Swiper (reads from Rooms config tab)
$gallerySwiper = $this->params->get('rooms_enable_swiper', 0);
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

// Rates grid
$showRates  = (int) $this->params->get('rooms_show_rates', 0);
$layoutPath = JPATH_SITE . '/components/com_accommodation_manager/layouts';

if ($showRates && !empty($this->periods))
{
	/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
	$rwa = $this->document->getWebAssetManager();

	if ((int) $this->params->get('rates_load_css', 1)) {
		$rwa->useStyle('com_accommodation_manager.rates-grid');
	}
	if ((int) $this->params->get('rates_load_js', 1)) {
		$rwa->useScript('com_accommodation_manager.rates-grid');
	}
}

// Request/booking buttons (show if URL is configured, no toggle needed)
$lang       = Accommodation_managerHelper::getLanguageSuffix();
$requestUrl = $this->params->get('request_link_' . $lang, '');
$bookingUrl = $this->params->get('booking_link_' . $lang, '');
?>
<div class="com-accommodation-manager-room">
	<?php if (empty($item)) : ?>
		<div class="alert alert-info">
			<?php echo Text::_('COM_ACCOMMODATION_MANAGER_ROOM_NOT_FOUND'); ?>
		</div>
	<?php else : ?>
		<article class="room-detail" data-category="<?php echo (int) $item->room_category; ?>">

			<?php if (!empty($item->title)) : ?>
				<h1 class="room-title"><?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?></h1>
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
			<?php
			$priceDisplay = $this->params->get('rooms_price_display', 'price_from');
			$showPrice    = ($priceDisplay !== 'none');
			$priceValue   = null;

			if ($priceDisplay === 'current_rate' && $item->current_rate !== null)
			{
				$priceValue = $item->current_rate;
			}
			elseif ($priceDisplay === 'price_from')
			{
				$priceValue = $item->room_price_from ?? null;
			}
			?>
			<?php echo LayoutHelper::render('room.info', [
				'surface'       => $item->room_surface ?? '',
				'people'        => $item->room_people ?? '',
				'price'         => $priceValue,
				'price_display' => $priceDisplay,
				'show'          => ['surface' => true, 'people' => true, 'price' => $showPrice],
				'langPrefix'    => 'COM_ACCOMMODATION_MANAGER',
			], $layoutPath); ?>

			<?php // Intro ?>
			<?php if (!empty($item->intro)) : ?>
				<div class="room-intro">
					<?php echo $item->intro; ?>
				</div>
			<?php endif; ?>

			<?php // Description ?>
			<?php if (!empty($item->description)) : ?>
				<div class="room-description">
					<?php echo $item->description; ?>
				</div>
			<?php endif; ?>

			<?php // Floor plan ?>
			<?php echo LayoutHelper::render('room.floor-plan', [
				'src'        => $item->floor_plan ?? '',
				'alt'        => $item->floor_plan_alt ?? '',
				'headingTag' => 'h2',
				'langPrefix' => 'COM_ACCOMMODATION_MANAGER',
			], $layoutPath); ?>

			<?php // Gallery ?>
			<?php if (!empty($item->gallery_items)) : ?>
				<?php echo LayoutHelper::render('room.gallery', [
					'items'        => $item->gallery_items,
					'swiper'       => $gallerySwiper,
					'swiperConfig' => $gallerySwiperConfig,
				], $layoutPath); ?>
			<?php endif; ?>

			<?php // Video ?>
			<?php if (!empty($item->video)) : ?>
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
			<?php echo LayoutHelper::render('room.actions', [
				'requestUrl'     => $requestUrl,
				'bookingUrl'     => $bookingUrl,
				'showRequestBtn' => true,
				'showBookingBtn' => true,
				'langPrefix'     => 'COM_ACCOMMODATION_MANAGER',
			], $layoutPath); ?>

		</article>
	<?php endif; ?>
</div>
