<?php
/**
 * @version    1.0.0
 * @package    Mod_Accommodation_Rooms
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

/** @var array $items */
$items = $items ?? [];

if (empty($items))
{
	return;
}

// Show/hide toggles
$showCategory    = (int) $params->get('show_category', 1);
$showSurface     = (int) $params->get('show_surface', 1);
$showPeople      = (int) $params->get('show_people', 1);
$priceDisplay    = $params->get('price_display', 'price_from');
$showPrice       = ($priceDisplay !== 'none');
$showIntro       = (int) $params->get('show_intro', 1);
$showDescription = (int) $params->get('show_description', 1);
$showFloorPlan   = (int) $params->get('show_floor_plan', 1);
$showGallery     = (int) $params->get('show_gallery', 1);
$showVideo       = (int) $params->get('show_video', 1);
$showInfo        = $showSurface || $showPeople || $showPrice;

// Layout path for shared layouts
$layoutPath = JPATH_SITE . '/components/com_accommodation_manager/layouts';

// Request/booking buttons
$showRequestBtn = (int) $params->get('show_request_button', 1);
$showBookingBtn = (int) $params->get('show_booking_button', 1);
$lang           = Accommodation_managerHelper::getLanguageSuffix();
$componentParams = ComponentHelper::getParams('com_accommodation_manager');
$requestUrl     = $componentParams->get('request_link_' . $lang, '');
$bookingUrl     = $componentParams->get('booking_link_' . $lang, '');

// Detail link
$showDetailLink = (int) $params->get('show_detail_link', 0);
?>
<ul class="mod-accommodation-rooms mod-listview-rooms <?php echo htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8'); ?>">
	<?php foreach ($items as $item) : ?>
    <li>
        <p><strong><?php echo $item->intro; ?></strong></p>
        <p><?php echo $item->description; ?></p>
    </li>
    <?php endforeach; ?>
</ul>
