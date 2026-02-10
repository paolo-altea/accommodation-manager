<?php
/**
 * @version    1.0.0
 * @package    Mod_Accommodation_Categories
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var array $items */
$items = $items ?? [];

if (empty($items))
{
	return;
}

$showImage       = (int) $params->get('show_image', 1);
$showDescription = (int) $params->get('show_description', 1);
$showLinkButton  = (int) $params->get('show_link_button', 1);
$titleTag        = in_array($params->get('title_tag', 'p'), ['p', 'h2', 'h3'], true) ? $params->get('title_tag', 'p') : 'p';
?>
<div class="mod-accommodation-categories <?php echo htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8'); ?>">
	<div class="categories-list">
		<?php foreach ($items as $item) :
			$categoryUrl = Route::_('index.php?option=com_accommodation_manager&view=category&id=' . (int) $item->id);
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
					<<?php echo $titleTag; ?> class="category-title">
						<a href="<?php echo $categoryUrl; ?>">
							<?php echo htmlspecialchars($item->name ?? '', ENT_QUOTES, 'UTF-8'); ?>
						</a>
					</<?php echo $titleTag; ?>>
					<?php if ($showDescription && !empty($item->description)) : ?>
						<div class="category-description">
							<?php echo $item->description; ?>
						</div>
					<?php endif; ?>
					<?php if ($showLinkButton) : ?>
						<a class="btn btn-primary category-link" href="<?php echo $categoryUrl; ?>">
							<?php echo Text::_('MOD_ACCOMMODATION_CATEGORIES_VIEW_ROOMS'); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
