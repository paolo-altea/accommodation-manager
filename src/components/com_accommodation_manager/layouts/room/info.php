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
 *   - surface    (int|null)
 *   - people     (string)
 *   - price_from (float|null)
 *   - show       (array)  ['surface' => bool, 'people' => bool, 'price_from' => bool]
 *   - langPrefix (string) 'COM_ACCOMMODATION_MANAGER' | 'MOD_ACCOMMODATION_ROOMS'
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$surface   = $displayData['surface'] ?? null;
$people    = $displayData['people'] ?? '';
$priceFrom = $displayData['price_from'] ?? null;
$show      = $displayData['show'] ?? [];
$prefix    = $displayData['langPrefix'] ?? 'COM_ACCOMMODATION_MANAGER';

$showSurface   = !empty($show['surface']);
$showPeople    = !empty($show['people']);
$showPriceFrom = !empty($show['price_from']);

if (!$showSurface && !$showPeople && !$showPriceFrom)
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

	<?php if ($showPriceFrom && $priceFrom !== null && $priceFrom > 0) : ?>
		<span class="room-price-from">
			<?php echo Text::sprintf($prefix . '_ROOM_PRICE_FROM_FORMAT', number_format((float) $priceFrom, 2, ',', '.')); ?>
		</span>
	<?php endif; ?>
</div>
