<?php
/**
 * @version    3.0.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2019. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var \Accomodationmanager\Component\Accommodation_manager\Administrator\View\Managerrates\HtmlView $this */

// Get data from the model
$model      = $this->getModel();
$periods    = $model->getPeriods();
$rooms      = $model->getRooms();
$typologies = $model->getTypologies();
$ratesGrid  = $model->getRatesGrid();
$pagination = $model->getPeriodsPagination();

// Get current language for multilingual fields
$lang = substr(Factory::getApplication()->getLanguage()->getTag(), 0, 2);

// Count typologies for rowspan
$typologyCount = count($typologies);

// Load component stylesheet
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('com_accommodation_manager.admin', 'administrator/components/com_accommodation_manager/assets/css/accommodation_manager.css');
?>

<form action="<?php echo Route::_('index.php?option=com_accommodation_manager&view=managerrates'); ?>"
      method="post"
      name="adminForm"
      id="adminForm"
      class="form-validate">

    <div class="rate-table-box">
        <div class="row mb-3">
            <div class="col-md-6">
                <a href="<?php echo Route::_('index.php?option=com_accommodation_manager&view=managerrateperiod&layout=edit'); ?>"
                   class="btn btn-info btn-sm">
                    <span class="icon-new" aria-hidden="true"></span>
                    <?php echo Text::_('COM_ACCOMMODATION_MANAGER_ADD_NEW_PERIOD'); ?>
                </a>
            </div>
            <div class="col-md-6 text-end">
                <button type="submit" class="btn btn-success btn-sm" onclick="document.getElementById('task').value='managerrates.saveGrid';">
                    <span class="icon-save" aria-hidden="true"></span>
                    <?php echo Text::_('COM_ACCOMMODATION_MANAGER_SAVE_RATES'); ?>
                </button>
            </div>
        </div>

        <?php if (empty($periods) && $pagination->pagesTotal == 0) : ?>
            <div class="alert alert-info">
                <?php echo Text::_('COM_ACCOMMODATION_MANAGER_NO_PERIODS_DEFINED'); ?>
            </div>
        <?php elseif (empty($rooms)) : ?>
            <div class="alert alert-info">
                <?php echo Text::_('COM_ACCOMMODATION_MANAGER_NO_ROOMS_DEFINED'); ?>
            </div>
        <?php elseif (empty($typologies)) : ?>
            <div class="alert alert-info">
                <?php echo Text::_('COM_ACCOMMODATION_MANAGER_NO_TYPOLOGIES_DEFINED'); ?>
            </div>
        <?php else : ?>
            <div class="rates-grid-wrapper">
                <table class="table table-bordered rates-grid">
                    <thead>
                        <tr>
                            <th scope="col" class="sticky-col sticky-header"><?php echo Text::_('COM_ACCOMMODATION_MANAGER_TITLE_MANAGERRATEPERIODS'); ?></th>
                            <th scope="col"><?php echo Text::_('COM_ACCOMMODATION_MANAGER_TITLE_MANAGERRATETYPOLOGIES'); ?></th>
                            <?php foreach ($rooms as $room) : ?>
                                <th scope="col" class="text-center">
                                    <?php echo htmlspecialchars($room->room_name, ENT_QUOTES, 'UTF-8'); ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($periods as $period) : ?>
                            <?php
                            $periodTitleField = 'period_title_' . $lang;
                            $periodTitle = !empty($period->$periodTitleField)
                                ? $period->$periodTitleField
                                : (!empty($period->period_title_en) ? $period->period_title_en : '');
                            // Format dates in locale format
                            $periodStart = HTMLHelper::_('date', $period->period_start, Text::_('DATE_FORMAT_LC4'));
                            $periodEnd = HTMLHelper::_('date', $period->period_end, Text::_('DATE_FORMAT_LC4'));
                            $periodLabel = $periodTitle
                                ? $periodTitle . '<br><small>' . $periodStart . ' - ' . $periodEnd . '</small>'
                                : $periodStart . ' - ' . $periodEnd;
                            ?>
                            <?php $firstTypology = true; ?>
                            <?php foreach ($typologies as $typology) : ?>
                                <?php
                                $typologyTitleField = 'rate_typology_' . $lang;
                                $typologyTitle = !empty($typology->$typologyTitleField)
                                    ? $typology->$typologyTitleField
                                    : (!empty($typology->rate_typology_en)
                                        ? $typology->rate_typology_en
                                        : (!empty($typology->rate_typology_title)
                                            ? $typology->rate_typology_title
                                            : 'Type #' . $typology->id));
                                ?>
                                <tr>
                                    <?php if ($firstTypology) : ?>
                                        <td class="period-cell sticky-col" rowspan="<?php echo $typologyCount; ?>">
                                            <strong><?php echo $periodLabel; ?></strong>
                                        </td>
                                        <?php $firstTypology = false; ?>
                                    <?php endif; ?>
                                    <td class="typology-cell">
                                        <?php echo htmlspecialchars($typologyTitle, ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <?php foreach ($rooms as $room) : ?>
                                        <?php
                                        $currentRate = $ratesGrid[$period->id][$room->id][$typology->id]['rate'] ?? '';
                                        $inputName = "rates[{$period->id}][{$room->id}][{$typology->id}]";
                                        ?>
                                        <td class="rate-input-cell">
                                            <input type="text"
                                                   name="<?php echo $inputName; ?>"
                                                   value="<?php echo htmlspecialchars($currentRate, ENT_QUOTES, 'UTF-8'); ?>"
                                                   class="form-control form-control-sm rate-input text-center"
                                                   inputmode="decimal"
                                                   pattern="[0-9]*[.,]?[0-9]*">
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-toolbar mt-3">
                <div class="limit-box">
                    <label for="limit" class="visually-hidden"><?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?></label>
                    <?php echo $pagination->getLimitBox(); ?>
                </div>
                <?php echo $pagination->getPagesLinks(); ?>
            </div>

        <?php endif; ?>

        <div class="row mt-3">
            <div class="col-md-6">
                <a href="<?php echo Route::_('index.php?option=com_accommodation_manager&view=managerrateperiod&layout=edit'); ?>"
                   class="btn btn-info btn-sm">
                    <span class="icon-new" aria-hidden="true"></span>
                    <?php echo Text::_('COM_ACCOMMODATION_MANAGER_ADD_NEW_PERIOD'); ?>
                </a>
            </div>
            <div class="col-md-6 text-end">
                <button type="submit" class="btn btn-success btn-sm" onclick="document.getElementById('task').value='managerrates.saveGrid';">
                    <span class="icon-save" aria-hidden="true"></span>
                    <?php echo Text::_('COM_ACCOMMODATION_MANAGER_SAVE_RATES'); ?>
                </button>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" id="task" value="">
    <input type="hidden" name="boxchecked" value="0">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<style>
.rate-table-box {
    padding: 1rem;
}

/* Grid wrapper for horizontal scroll */
.rates-grid-wrapper {
    overflow-x: auto;
    max-width: 100%;
    margin-bottom: 1rem;
}

.rates-grid {
    margin-bottom: 0;
}

.rates-grid th {
    white-space: nowrap;
    font-size: 0.9rem;
}

/* Sticky first column */
.sticky-col {
    position: sticky;
    left: 0;
    z-index: 1;
    background: inherit;
}

.sticky-header {
    z-index: 2;
}

.period-cell {
    vertical-align: middle;
    min-width: 150px;
    border-right: 2px solid var(--bs-border-color) !important;
}

.period-cell small {
    color: var(--bs-secondary-color);
}

.typology-cell {
    white-space: nowrap;
    font-size: 0.85rem;
}

.rate-input-cell {
    padding: 0.25rem !important;
    vertical-align: middle;
}

.rate-input {
    width: 80px;
    font-size: 0.85rem;
}

.pagination-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}
</style>
