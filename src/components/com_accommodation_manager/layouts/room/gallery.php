<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 *
 * Layout: room gallery with optional Swiper slider.
 *
 * Expected $displayData keys:
 *   - items        (array)  Gallery item objects with image, image_mobile, alt
 *   - swiper       (bool)   Whether to render as Swiper slider
 *   - swiperConfig (array)  Swiper options (only used when swiper=true):
 *       slidesPerViewMobile, slidesPerViewDesktop,
 *       spaceBetweenMobile, spaceBetweenDesktop,
 *       autoplay, autoplayDelay, navigation, pagination
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

$items       = $displayData['items'] ?? [];
$swiper      = !empty($displayData['swiper']);
$swiperConfig = $displayData['swiperConfig'] ?? [];

if (empty($items))
{
	return;
}
?>
<?php if ($swiper) : ?>
<div class="room-gallery am-gallery-swiper swiper"
	 data-slides-per-view-mobile="<?php echo (float) ($swiperConfig['slidesPerViewMobile'] ?? 1); ?>"
	 data-slides-per-view-desktop="<?php echo (float) ($swiperConfig['slidesPerViewDesktop'] ?? 1); ?>"
	 data-space-between-mobile="<?php echo (int) ($swiperConfig['spaceBetweenMobile'] ?? 10); ?>"
	 data-space-between-desktop="<?php echo (int) ($swiperConfig['spaceBetweenDesktop'] ?? 30); ?>"
	 data-autoplay="<?php echo !empty($swiperConfig['autoplay']) ? (int) ($swiperConfig['autoplayDelay'] ?? 5000) : '0'; ?>"
	 data-navigation="<?php echo (int) ($swiperConfig['navigation'] ?? 1); ?>"
	 data-pagination="<?php echo (int) ($swiperConfig['pagination'] ?? 1); ?>">
	<div class="swiper-wrapper">
<?php else : ?>
<div class="room-gallery">
<?php endif; ?>
	<?php foreach ($items as $galleryItem) : ?>
		<?php if (!empty($galleryItem->image)) :
			$imgData = HTMLHelper::_('cleanImageURL', $galleryItem->image);
			$imgW    = $imgData->attributes['width'] ?? null;
			$imgH    = $imgData->attributes['height'] ?? null;

			// Subform media fields don't store the #joomlaImage fragment,
			// so fall back to getimagesize() for dimensions
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
			<?php if ($swiper) : ?><div class="swiper-slide"><?php endif; ?>
			<picture class="room-gallery__picture">
				<?php if ($mobileUrl) : ?>
					<source media="(max-width: 768px)" srcset="<?php echo htmlspecialchars($mobileUrl, ENT_QUOTES, 'UTF-8'); ?>">
				<?php endif; ?>
				<?php echo HTMLHelper::_('image', $imgData->url, $galleryItem->alt ?? '', $imgAttribs); ?>
			</picture>
			<?php if ($swiper) : ?></div><?php endif; ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php if ($swiper) : ?>
	</div>
	<?php if (!empty($swiperConfig['navigation'])) : ?>
		<div class="swiper-button-prev"></div>
		<div class="swiper-button-next"></div>
	<?php endif; ?>
	<?php if (!empty($swiperConfig['pagination'])) : ?>
		<div class="swiper-pagination"></div>
	<?php endif; ?>
<?php endif; ?>
</div>
