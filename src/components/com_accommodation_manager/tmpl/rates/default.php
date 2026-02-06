<?php
/**
 * @version    3.2.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$periods    = $this->periods;
$rooms      = $this->rooms;
$typologies = $this->typologies;
$grid       = $this->grid;
$numRooms   = count($rooms);
?>
<div class="com-accommodation-manager-rates">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading') ?: $this->params->get('page_title')); ?></h1>
	<?php endif; ?>

	<?php if (empty($periods) || empty($rooms) || empty($typologies)) : ?>
		<div class="alert alert-info">
			<?php echo Text::_('COM_ACCOMMODATION_MANAGER_NO_RATES'); ?>
		</div>
	<?php else : ?>
		<div class="rates-grid-wrapper">
			<table class="rates-grid">
				<thead>
					<tr>
						<th class="rates-grid__header-period">
							<?php echo Text::_('COM_ACCOMMODATION_MANAGER_RATES_PERIOD'); ?>
						</th>
						<th class="rates-grid__header-typology">
							<?php echo Text::_('COM_ACCOMMODATION_MANAGER_RATES_TYPOLOGY'); ?>
						</th>
						<?php foreach ($rooms as $room) : ?>
							<th class="rates-grid__header-room">
								<?php echo htmlspecialchars($room->title ?: $room->room_name, ENT_QUOTES, 'UTF-8'); ?>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($periods as $period) :
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
	<?php endif; ?>
</div>
