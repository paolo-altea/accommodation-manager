<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

$showImage       = $this->params->get('categories_show_image', 1);
$showDescription = $this->params->get('categories_show_description', 1);
$showLinkButton  = $this->params->get('categories_show_link_button', 0);

$layoutPath = JPATH_SITE . '/components/com_accommodation_manager/layouts';
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
				<?php echo LayoutHelper::render('category.item', [
					'item'            => $item,
					'categoryUrl'     => Route::_('index.php?option=com_accommodation_manager&view=category&id=' . (int) $item->id),
					'showImage'       => $showImage,
					'showDescription' => $showDescription,
					'showLinkButton'  => $showLinkButton,
					'titleTag'        => 'h2',
					'langPrefix'      => 'COM_ACCOMMODATION_MANAGER',
				], $layoutPath); ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
