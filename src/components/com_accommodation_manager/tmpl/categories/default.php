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

$showImage       = $this->params->get('categories_show_image', 1);
$showDescription = $this->params->get('categories_show_description', 1);
$showLinkButton  = $this->params->get('categories_show_link_button', 0);
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
		<div class="categories-list">
			<?php foreach ($this->items as $item) :
				$categoryUrl = Route::_('index.php?option=com_accommodation_manager&view=rooms&category_id=' . (int) $item->id);
			?>
				<div class="category-item">
					<?php if ($showImage && !empty($item->image)) :
						$imgData    = HTMLHelper::_('cleanImageURL', $item->image);
						$imgAttribs = ['loading' => 'lazy'];
						if (!empty($imgData->attributes['width'])) {
							$imgAttribs['width'] = (int) $imgData->attributes['width'];
						}
						if (!empty($imgData->attributes['height'])) {
							$imgAttribs['height'] = (int) $imgData->attributes['height'];
						}
					?>
						<div class="category-image">
							<a href="<?php echo $categoryUrl; ?>">
								<?php echo HTMLHelper::_('image', $imgData->url, $item->image_alt ?? '', $imgAttribs); ?>
							</a>
						</div>
					<?php endif; ?>
					<div class="category-content">
						<h2 class="category-title">
							<a href="<?php echo $categoryUrl; ?>">
								<?php echo htmlspecialchars($item->name ?? '', ENT_QUOTES, 'UTF-8'); ?>
							</a>
						</h2>
						<?php if ($showDescription && !empty($item->description)) : ?>
							<div class="category-description">
								<?php echo $item->description; ?>
							</div>
						<?php endif; ?>
						<?php if ($showLinkButton) : ?>
							<a class="btn btn-primary category-link" href="<?php echo $categoryUrl; ?>">
								<?php echo Text::_('COM_ACCOMMODATION_MANAGER_VIEW_ROOMS'); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
