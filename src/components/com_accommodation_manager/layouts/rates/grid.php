<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 *
 * Layout: rates grid with all rooms as columns.
 *
 * Expected $displayData keys:
 *   - periods    (array)  Rate period objects
 *   - rooms      (array)  Room objects
 *   - typologies (array)  Rate typology objects
 *   - grid       (array)  3D array [period_id][room_id][typology_id] => rate
 *   - params     (Registry) Component params
 */

defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$periods    = $displayData['periods'] ?? [];
$rooms      = $displayData['rooms'] ?? [];
$typologies = $displayData['typologies'] ?? [];
$grid       = $displayData['grid'] ?? [];
$params     = $displayData['params'];

$tables = Accommodation_managerHelper::buildSeasonGroups($periods, $params);

if (empty($periods) || empty($rooms) || empty($typologies)) : ?>
	<div class="alert alert-info">
		<?php echo Text::_('COM_ACCOMMODATION_MANAGER_NO_RATES'); ?>
	</div>
<?php else : ?>
	<?php foreach ($tables as $table) : ?>
		<?php if (!empty($table['heading'])) : ?>
			<h2 class="rates-season-heading"><?php echo $table['heading']; ?></h2>
		<?php endif; ?>

		<div class="rates-grid-wrapper">
			<table class="rates-grid">
				<thead>
					<tr>
						<th class="rates-grid__header-period" scope="col">
							<?php echo Text::_('COM_ACCOMMODATION_MANAGER_RATES_PERIOD'); ?>
						</th>
						<th class="rates-grid__header-typology" scope="col">
							<?php echo Text::_('COM_ACCOMMODATION_MANAGER_RATES_TYPOLOGY'); ?>
						</th>
						<?php foreach ($rooms as $room) : ?>
							<th class="rates-grid__header-room" scope="col">
								<?php echo htmlspecialchars($room->title ?: $room->room_name, ENT_QUOTES, 'UTF-8'); ?>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($table['periods'] as $period) :
						$numTypologies = count($typologies);
						$isFirst       = true;
					?>
						<?php foreach ($typologies as $typology) : ?>
							<tr class="rates-grid__row">
								<?php if ($isFirst) : ?>
									<td class="rates-grid__period" rowspan="<?php echo $numTypologies; ?>">
										<span class="rates-grid__period-title">
											<?php echo htmlspecialchars($period->title ?? '', ENT_QUOTES, 'UTF-8'); ?>
										</span>
										<span class="rates-grid__period-dates">
											<?php echo HTMLHelper::_('date', $period->period_start, Text::_('DATE_FORMAT_LC4')); ?>
											&ndash;
											<?php echo HTMLHelper::_('date', $period->period_end, Text::_('DATE_FORMAT_LC4')); ?>
										</span>
									</td>
								<?php endif; ?>
								<td class="rates-grid__typology">
									<?php echo htmlspecialchars($typology->title ?: $typology->title_fallback, ENT_QUOTES, 'UTF-8'); ?>
								</td>
								<?php foreach ($rooms as $room) :
									$rate = $grid[(int) $period->id][(int) $room->id][(int) $typology->id] ?? null;
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
							<?php $isFirst = false; ?>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
