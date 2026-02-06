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
use Joomla\CMS\Uri\Uri;

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
			<?php foreach ($this->items as $item) : ?>
				<div class="category-item">
					<?php if (!empty($item->image)) :
						$imgData = HTMLHelper::_('cleanImageURL', $item->image);
						$imgUrl  = Uri::root(true) . '/' . $imgData->url;
						$imgW    = $imgData->attributes['width'] ?? null;
						$imgH    = $imgData->attributes['height'] ?? null;
					?>
						<div class="category-image">
							<a href="<?php echo Route::_('index.php?option=com_accommodation_manager&view=rooms&category_id=' . (int) $item->id); ?>">
								<img src="<?php echo htmlspecialchars($imgUrl, ENT_QUOTES, 'UTF-8'); ?>"
									alt="<?php echo htmlspecialchars($item->image_alt ?? '', ENT_QUOTES, 'UTF-8'); ?>"
									<?php if ($imgW && $imgH) : ?>
										width="<?php echo (int) $imgW; ?>"
										height="<?php echo (int) $imgH; ?>"
									<?php endif; ?>
									loading="lazy" />
							</a>
						</div>
					<?php endif; ?>
					<div class="category-content">
						<h2 class="category-title">
							<a href="<?php echo Route::_('index.php?option=com_accommodation_manager&view=rooms&category_id=' . (int) $item->id); ?>">
								<?php echo htmlspecialchars($item->name ?? '', ENT_QUOTES, 'UTF-8'); ?>
							</a>
						</h2>
						<?php if (!empty($item->description)) : ?>
							<div class="category-description">
								<?php echo $item->description; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
