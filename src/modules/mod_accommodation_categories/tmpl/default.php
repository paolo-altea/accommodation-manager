<?php
/**
 * @version    1.0.0
 * @package    Mod_Accommodation_Categories
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
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

$layoutPath = JPATH_SITE . '/components/com_accommodation_manager/layouts';
?>
<div class="mod-accommodation-categories <?php echo htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8'); ?>">
	<div class="categories-list">
		<?php foreach ($items as $item) : ?>
			<?php echo LayoutHelper::render('category.item', [
				'item'            => $item,
				'categoryUrl'     => Route::_('index.php?option=com_accommodation_manager&view=category&id=' . (int) $item->id),
				'showImage'       => $showImage,
				'showDescription' => $showDescription,
				'showLinkButton'  => $showLinkButton,
				'titleTag'        => $titleTag,
				'langPrefix'      => 'MOD_ACCOMMODATION_CATEGORIES',
			], $layoutPath); ?>
		<?php endforeach; ?>
	</div>
</div>
