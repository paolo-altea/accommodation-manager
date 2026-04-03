<?php
/**
 * @version    3.5.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 *
 * Layout: room basic info (surface, people, price from).
 *
 * Expected $displayData keys:
 *   - surface       (int|null)
 *   - people        (string)
 *   - price         (float|null)  Unified price value (current_rate or price_from)
 *   - price_display (string)      'price_from'|'current_rate'|'none' (default: 'price_from')
 *   - show          (array)       ['surface' => bool, 'people' => bool, 'price' => bool]
 *   - langPrefix    (string)      'COM_ACCOMMODATION_MANAGER' | 'MOD_ACCOMMODATION_ROOMS'
 *
 * Legacy support: if 'price_from' key is passed instead of 'price', it still works.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$surface      = $displayData['surface'] ?? null;
$people       = $displayData['people'] ?? '';
$show         = $displayData['show'] ?? [];
$prefix       = $displayData['langPrefix'] ?? 'COM_ACCOMMODATION_MANAGER';
$priceDisplay = $displayData['price_display'] ?? 'price_from';

// Unified price: new 'price' key, fallback to legacy 'price_from'
$price = $displayData['price'] ?? $displayData['price_from'] ?? null;

$showSurface = !empty($show['surface']);
$showPeople  = !empty($show['people']);
$showPrice   = !empty($show['price']) || !empty($show['price_from']);

if (!$showSurface && !$showPeople && !$showPrice)
{
	return;
}
?>
<div class="room-info">
	<?php if ($showSurface && !empty($surface)) : ?>
		<span class="room-surface">
			<?php echo Text::_($prefix . '_ROOM_SURFACE'); ?>:
			<?php echo (int) $surface; ?> <?php echo Text::_($prefix . '_ROOM_SURFACE_UNIT'); ?>
		</span>
	<?php endif; ?>

	<?php if ($showPeople && !empty($people)) : ?>
		<span class="room-people">
			<?php echo Text::_($prefix . '_ROOM_PEOPLE'); ?>:
			<?php echo htmlspecialchars($people, ENT_QUOTES, 'UTF-8'); ?>
		</span>
	<?php endif; ?>

	<?php if ($showPrice && $price !== null && (float) $price > 0) : ?>
		<span class="room-price">
			<?php echo Text::sprintf($prefix . '_ROOM_PRICE_FROM_FORMAT', number_format((float) $price, 2, ',', '.')); ?>
		</span>
	<?php endif; ?>
</div>
