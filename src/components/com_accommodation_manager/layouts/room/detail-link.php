<?php
/**
 * @version    3.3.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 *
 * Layout: "View room detail" link button.
 *
 * Expected $displayData keys:
 *   - roomId     (int)    Room ID
 *   - langPrefix (string) 'COM_ACCOMMODATION_MANAGER' | 'MOD_ACCOMMODATION_ROOMS'
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$roomId = (int) ($displayData['roomId'] ?? 0);
$prefix = $displayData['langPrefix'] ?? 'COM_ACCOMMODATION_MANAGER';

if (!$roomId)
{
	return;
}
?>
<div class="room-detail-link">
	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_accommodation_manager&view=room&id=' . $roomId); ?>">
		<?php echo Text::_($prefix . '_ROOM_DETAIL'); ?>
	</a>
</div>
