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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

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

if ($gallerySwiper)
{
	$galSlidesPerViewMobile  = (float) $params->get('gallery_slides_per_view_mobile', 1);
	$galSlidesPerViewDesktop = (float) $params->get('gallery_slides_per_view_desktop', 1);
	$galSpaceBetweenMobile   = (int) $params->get('gallery_space_between_mobile', 10);
	$galSpaceBetweenDesktop  = (int) $params->get('gallery_space_between_desktop', 30);
	$galAutoplay             = (int) $params->get('gallery_autoplay', 0);
	$galAutoplayDelay        = (int) $params->get('gallery_autoplay_delay', 5000);
	$galNavigation           = (int) $params->get('gallery_navigation', 1);
	$galPagination           = (int) $params->get('gallery_pagination', 1);
	$galLoadCss              = (int) $params->get('load_css', 1);
	$galLoadJs               = (int) $params->get('load_js', 1);

	/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
	$wa = $app->getDocument()->getWebAssetManager();
	$wa->getRegistry()->addRegistryFile('media/com_accommodation_manager/joomla.asset.json');

	if ($galLoadJs) {
		$wa->useScript('com_accommodation_manager.gallery-slider');
	}
	if ($galLoadCss) {
		$wa->useStyle('com_accommodation_manager.gallery-slider');
	}
}

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
						<?php echo Text::_('MOD_ACCOMMODATION_ROOMS_ROOM_SURFACE'); ?>:
						<?php echo htmlspecialchars($item->room_surface, ENT_QUOTES, 'UTF-8'); ?>
					</span>
				<?php endif; ?>

				<?php if ($showPeople && !empty($item->room_people)) : ?>
					<span class="room-people">
						<?php echo Text::_('MOD_ACCOMMODATION_ROOMS_ROOM_PEOPLE'); ?>:
						<?php echo htmlspecialchars($item->room_people, ENT_QUOTES, 'UTF-8'); ?>
					</span>
				<?php endif; ?>

				<?php if ($showPriceFrom && !empty($item->room_price_from)) : ?>
					<span class="room-price-from">
						<?php echo Text::_('MOD_ACCOMMODATION_ROOMS_ROOM_PRICE_FROM'); ?>:
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
					<?php echo Text::_('MOD_ACCOMMODATION_ROOMS_ROOM_DETAIL'); ?>
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
				<h4><?php echo Text::_('MOD_ACCOMMODATION_ROOMS_ROOM_FLOOR_PLAN'); ?></h4>
				<?php echo HTMLHelper::_('image', $fpData->url, $item->floor_plan_alt ?? '', $fpAttribs); ?>
			</div>
		<?php endif; ?>

		<?php // Gallery ?>
		<?php if ($showGallery && !empty($item->gallery_items)) : ?>
			<?php if ($gallerySwiper) : ?>
			<div class="room-gallery am-gallery-swiper swiper"
				 data-slides-per-view-mobile="<?php echo $galSlidesPerViewMobile; ?>"
				 data-slides-per-view-desktop="<?php echo $galSlidesPerViewDesktop; ?>"
				 data-space-between-mobile="<?php echo $galSpaceBetweenMobile; ?>"
				 data-space-between-desktop="<?php echo $galSpaceBetweenDesktop; ?>"
				 data-autoplay="<?php echo $galAutoplay ? $galAutoplayDelay : '0'; ?>"
				 data-navigation="<?php echo $galNavigation; ?>"
				 data-pagination="<?php echo $galPagination; ?>">
				<div class="swiper-wrapper">
			<?php else : ?>
			<div class="room-gallery">
			<?php endif; ?>
				<?php foreach ($item->gallery_items as $galleryItem) : ?>
					<?php if (!empty($galleryItem->image)) :
						$imgData = HTMLHelper::_('cleanImageURL', $galleryItem->image);
						$imgW    = $imgData->attributes['width'] ?? null;
						$imgH    = $imgData->attributes['height'] ?? null;

						if (!$imgW || !$imgH)
						{
							$imgPath = JPATH_ROOT . '/' . $imgData->url;

							if (is_file($imgPath))
							{
								$size = getimagesize($imgPath);

								if ($size)
								{
									$imgW = $size[0];
									$imgH = $size[1];
								}
							}
						}

						$imgAttribs = ['loading' => 'lazy'];
						if ($imgW) {
							$imgAttribs['width'] = (int) $imgW;
						}
						if ($imgH) {
							$imgAttribs['height'] = (int) $imgH;
						}

						$mobileUrl = null;
						if (!empty($galleryItem->image_mobile))
						{
							$mobileData = HTMLHelper::_('cleanImageURL', $galleryItem->image_mobile);
							$mobileUrl  = $mobileData->url;
						}
					?>
						<?php if ($gallerySwiper) : ?><div class="swiper-slide"><?php endif; ?>
						<picture class="room-gallery__picture">
							<?php if ($mobileUrl) : ?>
								<source media="(max-width: 768px)" srcset="<?php echo htmlspecialchars($mobileUrl, ENT_QUOTES, 'UTF-8'); ?>">
							<?php endif; ?>
							<?php echo HTMLHelper::_('image', $imgData->url, $galleryItem->alt ?? '', $imgAttribs); ?>
						</picture>
						<?php if ($gallerySwiper) : ?></div><?php endif; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php if ($gallerySwiper) : ?>
				</div>
				<?php if ($galNavigation) : ?>
					<div class="swiper-button-prev"></div>
					<div class="swiper-button-next"></div>
				<?php endif; ?>
				<?php if ($galPagination) : ?>
					<div class="swiper-pagination"></div>
				<?php endif; ?>
			<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php // Video ?>
		<?php if ($showVideo && !empty($item->video)) : ?>
			<div class="room-video">
				<?php echo $item->video; ?>
			</div>
		<?php endif; ?>

		<?php // Request / Booking buttons ?>
		<?php if (($showRequestBtn && $requestUrl) || ($showBookingBtn && $bookingUrl)) : ?>
			<div class="room-actions">
				<?php if ($showRequestBtn && $requestUrl) : ?>
					<a class="btn btn-primary room-request-btn" href="<?php echo htmlspecialchars($requestUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
						<?php echo Text::_('MOD_ACCOMMODATION_ROOMS_REQUEST'); ?>
					</a>
				<?php endif; ?>
				<?php if ($showBookingBtn && $bookingUrl) : ?>
					<a class="btn btn-primary room-booking-btn" href="<?php echo htmlspecialchars($bookingUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
						<?php echo Text::_('MOD_ACCOMMODATION_ROOMS_BOOKING'); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</section>
	<?php endforeach; ?>
</div>
