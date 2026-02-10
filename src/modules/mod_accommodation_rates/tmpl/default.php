<?php
/**
 * @version    1.0.0
 * @package    Mod_Accommodation_Rates
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2026. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$periods    = $periods ?? [];
$rooms      = $rooms ?? [];
$typologies = $typologies ?? [];
$grid       = $grid ?? [];

$componentParams = ComponentHelper::getParams('com_accommodation_manager');
$splitBySeason   = (int) $componentParams->get('rates_split_by_season', 0);

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $app->getDocument()->getWebAssetManager();
$wa->getRegistry()->addRegistryFile('media/com_accommodation_manager/joomla.asset.json');

if ((int) $params->get('load_css', 1)) {
	$wa->useStyle('com_accommodation_manager.rates-grid');
}

if ((int) $params->get('load_js', 1)) {
	$wa->useScript('com_accommodation_manager.rates-grid');
}

// Build season groups if needed
$tables = [];

if ($splitBySeason && !empty($periods))
{
	$summerStartMonth = (int) $componentParams->get('rates_summer_start_month', 5);
	$summerStartDay   = (int) $componentParams->get('rates_summer_start_day', 1);
	$winterStartMonth = (int) $componentParams->get('rates_winter_start_month', 11);
	$winterStartDay   = (int) $componentParams->get('rates_winter_start_day', 1);

	$summerMMDD = $summerStartMonth * 100 + $summerStartDay;
	$winterMMDD = $winterStartMonth * 100 + $winterStartDay;

	$seasonGroups = [];

	foreach ($periods as $period)
	{
		$year       = (int) date('Y', strtotime($period->period_start));
		$month      = (int) date('m', strtotime($period->period_start));
		$day        = (int) date('d', strtotime($period->period_start));
		$periodMMDD = $month * 100 + $day;

		if ($periodMMDD >= $summerMMDD && $periodMMDD < $winterMMDD)
		{
			$key     = $year . '_1_summer';
			$heading = Text::_('MOD_ACCOMMODATION_RATES_SUMMER') . ' ' . $year;
		}
		elseif ($periodMMDD >= $winterMMDD)
		{
			$key     = $year . '_2_winter';
			$heading = Text::_('MOD_ACCOMMODATION_RATES_WINTER') . ' ' . $year . '/' . substr($year + 1, 2);
		}
		else
		{
			$key     = ($year - 1) . '_2_winter';
			$heading = Text::_('MOD_ACCOMMODATION_RATES_WINTER') . ' ' . ($year - 1) . '/' . substr($year, 2);
		}

		if (!isset($seasonGroups[$key]))
		{
			$seasonGroups[$key] = ['heading' => $heading, 'periods' => []];
		}

		$seasonGroups[$key]['periods'][] = $period;
	}

	foreach ($seasonGroups as $group)
	{
		$tables[] = $group;
	}
}
else
{
	$tables[] = ['heading' => '', 'periods' => $periods];
}
?>
<div class="mod-accommodation-rates accommodation-manager-rates <?php echo htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8'); ?>">
	<?php if (empty($periods) || empty($rooms) || empty($typologies)) : ?>
		<div class="alert alert-info">
			<?php echo Text::_('MOD_ACCOMMODATION_RATES_NO_RATES'); ?>
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
								<?php echo Text::_('MOD_ACCOMMODATION_RATES_PERIOD'); ?>
							</th>
							<th class="rates-grid__header-typology" scope="col">
								<?php echo Text::_('MOD_ACCOMMODATION_RATES_TYPOLOGY'); ?>
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
</div>
