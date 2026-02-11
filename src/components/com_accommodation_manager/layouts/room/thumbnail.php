<?php
/**
 * @version    3.3.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 *
 * Layout: room thumbnail image.
 *
 * Expected $displayData keys:
 *   - src  (string) Raw image path (with Joomla fragment)
 *   - alt  (string) Already-localised alt text
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

$src = $displayData['src'] ?? '';
$alt = $displayData['alt'] ?? '';

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
<div class="room-thumbnail">
	<?php echo HTMLHelper::_('image', $imgData->url, $alt, $imgAttribs); ?>
</div>
