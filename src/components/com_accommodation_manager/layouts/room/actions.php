<?php
/**
 * @version    3.3.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 *
 * Layout: request / booking action buttons.
 *
 * Expected $displayData keys:
 *   - requestUrl     (string)
 *   - bookingUrl     (string)
 *   - showRequestBtn (bool)
 *   - showBookingBtn (bool)
 *   - langPrefix     (string) 'COM_ACCOMMODATION_MANAGER' | 'MOD_ACCOMMODATION_ROOMS'
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$requestUrl     = $displayData['requestUrl'] ?? '';
$bookingUrl     = $displayData['bookingUrl'] ?? '';
$showRequestBtn = !empty($displayData['showRequestBtn']);
$showBookingBtn = !empty($displayData['showBookingBtn']);
$prefix         = $displayData['langPrefix'] ?? 'COM_ACCOMMODATION_MANAGER';

$hasRequest = $showRequestBtn && $requestUrl;
$hasBooking = $showBookingBtn && $bookingUrl;

if (!$hasRequest && !$hasBooking)
{
	return;
}
?>
<div class="room-actions">
	<?php if ($hasRequest) : ?>
		<a class="btn btn-primary room-request-btn" href="<?php echo htmlspecialchars($requestUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
			<?php echo Text::_($prefix . '_REQUEST'); ?>
		</a>
	<?php endif; ?>
	<?php if ($hasBooking) : ?>
		<a class="btn btn-primary room-booking-btn" href="<?php echo htmlspecialchars($bookingUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
			<?php echo Text::_($prefix . '_BOOKING'); ?>
		</a>
	<?php endif; ?>
</div>
