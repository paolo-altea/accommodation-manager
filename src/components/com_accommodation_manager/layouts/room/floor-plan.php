<?php
/**
 * @version    3.3.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 *
 * Layout: room floor plan image with heading.
 *
 * Expected $displayData keys:
 *   - src        (string) Raw image path (with Joomla fragment)
 *   - alt        (string) Already-localised alt text
 *   - headingTag (string) 'h2' | 'h3' | 'h4'
 *   - langPrefix (string) 'COM_ACCOMMODATION_MANAGER' | 'MOD_ACCOMMODATION_ROOMS'
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$src        = $displayData['src'] ?? '';
$alt        = $displayData['alt'] ?? '';
$headingTag = $displayData['headingTag'] ?? 'h3';
$prefix     = $displayData['langPrefix'] ?? 'COM_ACCOMMODATION_MANAGER';

if (empty($src))
{
	return;
}

$imgData    = HTMLHelper::_('cleanImageURL', $src);
$imgAttribs = ['loading' => 'lazy'];

if (!empty($imgData->attributes['width'])) {
	$imgAttribs['width'] = (int) $imgData->attributes['width'];
}
if (!empty($imgData->attributes['height'])) {
	$imgAttribs['height'] = (int) $imgData->attributes['height'];
}
?>
<div class="room-floor-plan">
	<<?php echo $headingTag; ?>><?php echo Text::_($prefix . '_ROOM_FLOOR_PLAN'); ?></<?php echo $headingTag; ?>>
	<?php echo HTMLHelper::_('image', $imgData->url, $alt, $imgAttribs); ?>
</div>
