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
use Joomla\CMS\Uri\Uri;

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
					$thumbData  = HTMLHelper::_('cleanImageURL', $item->thumbnail);
					$thumbUrl   = Uri::root(true) . '/' . $thumbData->url;
					$thumbW     = $thumbData->attributes['width'] ?? null;
					$thumbH     = $thumbData->attributes['height'] ?? null;
				?>
					<div class="room-thumbnail">
						<img src="<?php echo htmlspecialchars($thumbUrl, ENT_QUOTES, 'UTF-8'); ?>"
							alt="<?php echo htmlspecialchars($item->thumbnail_alt ?? '', ENT_QUOTES, 'UTF-8'); ?>"
							<?php if ($thumbW && $thumbH) : ?>
								width="<?php echo (int) $thumbW; ?>"
								height="<?php echo (int) $thumbH; ?>"
							<?php endif; ?>
							loading="lazy" />
					</div>
				<?php endif; ?>

				<?php // Basic info ?>
				<div class="room-info">
					<?php if (!empty($item->room_surface)) : ?>
						<span class="room-surface">
							<?php echo Text::_('COM_ACCOMMODATION_MANAGER_ROOM_SURFACE'); ?>:
							<?php echo htmlspecialchars($item->room_surface, ENT_QUOTES, 'UTF-8'); ?>
						</span>
					<?php endif; ?>

					<?php if (!empty($item->room_people)) : ?>
						<span class="room-people">
							<?php echo Text::_('COM_ACCOMMODATION_MANAGER_ROOM_PEOPLE'); ?>:
							<?php echo htmlspecialchars($item->room_people, ENT_QUOTES, 'UTF-8'); ?>
						</span>
					<?php endif; ?>

					<?php if (!empty($item->room_price_from)) : ?>
						<span class="room-price-from">
							<?php echo Text::_('COM_ACCOMMODATION_MANAGER_ROOM_PRICE_FROM'); ?>:
							<?php echo htmlspecialchars($item->room_price_from, ENT_QUOTES, 'UTF-8'); ?>
						</span>
					<?php endif; ?>
				</div>

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
				<?php if (!empty($item->floor_plan)) :
					$fpData = HTMLHelper::_('cleanImageURL', $item->floor_plan);
					$fpUrl  = Uri::root(true) . '/' . $fpData->url;
					$fpW    = $fpData->attributes['width'] ?? null;
					$fpH    = $fpData->attributes['height'] ?? null;
				?>
					<div class="room-floor-plan">
						<h3><?php echo Text::_('COM_ACCOMMODATION_MANAGER_ROOM_FLOOR_PLAN'); ?></h3>
						<img src="<?php echo htmlspecialchars($fpUrl, ENT_QUOTES, 'UTF-8'); ?>"
							alt="<?php echo htmlspecialchars($item->floor_plan_alt ?? '', ENT_QUOTES, 'UTF-8'); ?>"
							<?php if ($fpW && $fpH) : ?>
								width="<?php echo (int) $fpW; ?>"
								height="<?php echo (int) $fpH; ?>"
							<?php endif; ?>
							loading="lazy" />
					</div>
				<?php endif; ?>

				<?php // Gallery ?>
				<?php if (!empty($item->gallery_items)) : ?>
					<div class="room-gallery">
						<?php foreach ($item->gallery_items as $galleryItem) : ?>
							<?php if (!empty($galleryItem->image)) :
								$imgData = HTMLHelper::_('cleanImageURL', $galleryItem->image);
								$imgUrl  = Uri::root(true) . '/' . $imgData->url;
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

								$mobileUrl = null;
								if (!empty($galleryItem->image_mobile))
								{
									$mobileData = HTMLHelper::_('cleanImageURL', $galleryItem->image_mobile);
									$mobileUrl  = Uri::root(true) . '/' . $mobileData->url;
								}
							?>
								<picture class="room-gallery__picture">
									<?php if ($mobileUrl) : ?>
										<source media="(max-width: 768px)" srcset="<?php echo htmlspecialchars($mobileUrl, ENT_QUOTES, 'UTF-8'); ?>">
									<?php endif; ?>
									<img src="<?php echo htmlspecialchars($imgUrl, ENT_QUOTES, 'UTF-8'); ?>"
										alt="<?php echo htmlspecialchars($galleryItem->alt ?? '', ENT_QUOTES, 'UTF-8'); ?>"
										<?php if ($imgW && $imgH) : ?>
											width="<?php echo (int) $imgW; ?>"
											height="<?php echo (int) $imgH; ?>"
										<?php endif; ?>
										loading="lazy" />
								</picture>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php // Video ?>
				<?php if (!empty($item->video)) : ?>
					<div class="room-video">
						<?php echo $item->video; ?>
					</div>
				<?php endif; ?>

			</section>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
