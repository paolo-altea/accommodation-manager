<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 *
 * Layout: rates grid for a single room.
 * Periods as rows, typologies as columns.
 *
 * Expected $displayData keys:
 *   - periods    (array)  Rate period objects
 *   - typologies (array)  Rate typology objects
 *   - grid       (array)  3D array [period_id][room_id][typology_id] => rate
 *   - roomId     (int)    The room ID to display rates for
 *   - params     (Registry) Component params
 */

defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$periods    = $displayData['periods'] ?? [];
$typologies = $displayData['typologies'] ?? [];
$grid       = $displayData['grid'] ?? [];
$roomId     = (int) ($displayData['roomId'] ?? 0);
$params     = $displayData['params'];

$tables = Accommodation_managerHelper::buildSeasonGroups($periods, $params);

// Check if this room has any rates at all
$hasRates = false;

foreach ($grid as $periodRates)
{
	if (isset($periodRates[$roomId]))
	{
		$hasRates = true;
		break;
	}
}

if (empty($periods) || empty($typologies) || !$hasRates)
{
	return;
}
?>
<?php foreach ($tables as $table) : ?>
	<?php if (!empty($table['heading'])) : ?>
		<h3 class="rates-season-heading"><?php echo $table['heading']; ?></h3>
	<?php endif; ?>

	<div class="rates-grid-wrapper">
		<table class="rates-grid">
			<thead>
				<tr>
					<th class="rates-grid__header-period" scope="col">
						<?php echo Text::_('COM_ACCOMMODATION_MANAGER_RATES_PERIOD'); ?>
					</th>
					<?php foreach ($typologies as $typology) : ?>
						<th class="rates-grid__header-typology-col" scope="col">
							<?php echo htmlspecialchars($typology->title ?: $typology->title_fallback, ENT_QUOTES, 'UTF-8'); ?>
						</th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($table['periods'] as $period) : ?>
					<tr class="rates-grid__row">
						<td class="rates-grid__period">
							<span class="rates-grid__period-title">
								<?php echo htmlspecialchars($period->title ?? '', ENT_QUOTES, 'UTF-8'); ?>
							</span>
							<span class="rates-grid__period-dates">
								<?php echo HTMLHelper::_('date', $period->period_start, Text::_('DATE_FORMAT_LC4')); ?>
								&ndash;
								<?php echo HTMLHelper::_('date', $period->period_end, Text::_('DATE_FORMAT_LC4')); ?>
							</span>
						</td>
						<?php foreach ($typologies as $typology) :
							$rate = $grid[(int) $period->id][$roomId][(int) $typology->id] ?? null;
						?>
							<td class="rates-grid__rate">
								<?php if ($rate !== null) : ?>
									<?php echo number_format((float) $rate, 2, ',', '.'); ?> &euro;
								<?php else : ?>
									<span class="rates-grid__not-available">&ndash;</span>
								<?php endif; ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endforeach; ?>
