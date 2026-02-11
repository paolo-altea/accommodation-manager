<?php
/**
 * @version    3.3.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 *
 * Layout: single category card (image + title + description + link button).
 *
 * Expected $displayData keys:
 *   - item            (object)  Category with ->id, ->name, ->image, ->image_alt, ->description
 *   - categoryUrl     (string)  Pre-built Route URL
 *   - showImage       (bool)
 *   - showDescription (bool)
 *   - showLinkButton  (bool)
 *   - titleTag        (string)  'h2' | 'p' (default 'h2')
 *   - langPrefix      (string)  'COM_ACCOMMODATION_MANAGER' | 'MOD_ACCOMMODATION_CATEGORIES'
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$item            = $displayData['item'] ?? null;
$categoryUrl     = $displayData['categoryUrl'] ?? '';
$showImage       = !empty($displayData['showImage']);
$showDescription = !empty($displayData['showDescription']);
$showLinkButton  = !empty($displayData['showLinkButton']);
$titleTag        = $displayData['titleTag'] ?? 'h2';
$prefix          = $displayData['langPrefix'] ?? 'COM_ACCOMMODATION_MANAGER';

if (empty($item))
{
	return;
}
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
				<?php echo Text::_($prefix . '_VIEW_ROOMS'); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
