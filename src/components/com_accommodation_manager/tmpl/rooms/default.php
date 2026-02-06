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

// Layout
$splitByCategory = $this->params->get('rooms_split_by_category', 0);

// Request/booking buttons
$showRequestBtn = $this->params->get('rooms_show_request_button', 1);
$showBookingBtn = $this->params->get('rooms_show_booking_button', 1);
$lang           = Accommodation_managerHelper::getLanguageSuffix();
$requestUrl     = $this->params->get('request_link_' . $lang, '');
$bookingUrl     = $this->params->get('booking_link_' . $lang, '');

// Build the list of items to iterate, optionally grouped by category
if ($splitByCategory && !empty($this->items))
{
	$groupedItems = [];

	foreach ($this->items as $item)
	{
		$groupedItems[(int) $item->room_category][] = $item;
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
		<?php
		// Build a flat list of render instructions: optional category headers + items
		$renderList = [];

		if ($splitByCategory)
		{
			foreach ($groupedItems as $categoryId => $items)
			{
				$renderList[] = ['type' => 'heading', 'name' => $items[0]->category_name ?? ''];

				foreach ($items as $item)
				{
					$renderList[] = ['type' => 'item', 'item' => $item];
				}
			}
		}
		else
		{
			foreach ($this->items as $item)
			{
				$renderList[] = ['type' => 'item', 'item' => $item];
			}
		}
		?>

		<?php foreach ($renderList as $entry) : ?>
			<?php if ($entry['type'] === 'heading' && !empty($entry['name'])) : ?>
				<h2 class="rooms-category-heading"><?php echo htmlspecialchars($entry['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
			<?php elseif ($entry['type'] === 'item') :
				$item = $entry['item'];
			?>
			<section class="room-item" id="room-<?php echo htmlspecialchars($item->room_name, ENT_QUOTES, 'UTF-8'); ?>" data-category="<?php echo (int) $item->room_category; ?>">

				<?php if (!empty($item->title)) : ?>
					<h2 class="room-title"><?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?></h2>
				<?php endif; ?>

				<?php if (!$splitByCategory && !empty($item->category_name)) : ?>
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
					<div class="room-gallery">
						<?php foreach ($item->gallery_items as $galleryItem) : ?>
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
								<picture class="room-gallery__picture">
									<?php if ($mobileUrl) : ?>
										<source media="(max-width: 768px)" srcset="<?php echo htmlspecialchars($mobileUrl, ENT_QUOTES, 'UTF-8'); ?>">
									<?php endif; ?>
									<?php echo HTMLHelper::_('image', $imgData->url, $galleryItem->alt ?? '', $imgAttribs); ?>
								</picture>
							<?php endif; ?>
						<?php endforeach; ?>
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
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
