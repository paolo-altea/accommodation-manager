<?php
/**
 * @version    CVS: 3.0.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2019. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useStyle('com_accommodation_manager.admin')
    ->useScript('com_accommodation_manager.admin');

$app = Factory::getApplication();
$user = $app->getIdentity();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canOrder = $user->authorise('core.edit.state', 'com_accommodation_manager');
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_accommodation_manager&task=managerrateperiods.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
    HTMLHelper::_('draggablelist.draggable');
}
?>

<form action="<?php echo Route::_('index.php?option=com_accommodation_manager&view=managerrateperiods'); ?>" method="post"
      name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

                <div class="clearfix"></div>
                <table class="table table-striped" id="managerrateperiodList">
                    <thead>
                    <tr>
                        <th class="w-1 text-center">
                            <?php echo HTMLHelper::_('grid.checkall'); ?>
                        </th>
                        <?php if (isset($this->items[0]->ordering)) : ?>
                        <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
                        <?php endif; ?>
                        <th scope="col" class="w-5 text-center">
                            <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_START', 'a.period_start', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_END', 'a.period_end', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" class="d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_TITLE_DE', 'a.period_title_de', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" class="d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_TITLE_IT', 'a.period_title_it', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" class="d-none d-lg-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_TITLE_EN', 'a.period_title_en', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" class="d-none d-xl-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_TITLE_FR', 'a.period_title_fr', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" class="d-none d-xl-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_TITLE_ES', 'a.period_title_es', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" class="w-5 d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_FIELD_ID_LABEL', 'a.id', $listDirn, $listOrder); ?>
                        </th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <td colspan="12">
                            <?php echo $this->pagination->getListFooter(); ?>
                        </td>
                    </tr>
                    </tfoot>
                    <tbody <?php if ($saveOrder) : ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>"<?php endif; ?>>
                    <?php foreach ($this->items as $i => $item) :
                        $ordering = ($listOrder == 'a.ordering');
                        $canCreate = $user->authorise('core.create', 'com_accommodation_manager');
                        $canEdit = $user->authorise('core.edit', 'com_accommodation_manager');
                        $canCheckin = $user->authorise('core.manage', 'com_accommodation_manager');
                        $canChange = $user->authorise('core.edit.state', 'com_accommodation_manager');
                    ?>
                        <tr class="row<?php echo $i % 2; ?>" data-draggable-group="1" data-transition>
                            <td class="text-center">
                                <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                            </td>

                            <?php if (isset($this->items[0]->ordering)) : ?>
                            <td class="text-center d-none d-md-table-cell">
                                <?php
                                $iconClass = '';
                                if (!$canChange) {
                                    $iconClass = ' inactive';
                                } elseif (!$saveOrder) {
                                    $iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');
                                }
                                ?>
                                <span class="sortable-handler<?php echo $iconClass; ?>">
                                    <span class="icon-ellipsis-v" aria-hidden="true"></span>
                                </span>
                                <?php if ($canChange && $saveOrder) : ?>
                                    <input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order hidden">
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>

                            <td class="text-center">
                                <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'managerrateperiods.', $canChange, 'cb'); ?>
                            </td>

                            <td>
                                <?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
                                    <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'managerrateperiods.', $canCheckin); ?>
                                <?php endif; ?>
                                <?php if ($canEdit) : ?>
                                    <a href="<?php echo Route::_('index.php?option=com_accommodation_manager&task=managerrateperiod.edit&id=' . (int) $item->id); ?>">
                                        <?php echo !empty($item->period_start) ? HTMLHelper::_('date', $item->period_start, Text::_('DATE_FORMAT_LC4')) : '-'; ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo !empty($item->period_start) ? HTMLHelper::_('date', $item->period_start, Text::_('DATE_FORMAT_LC4')) : '-'; ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php echo !empty($item->period_end) ? HTMLHelper::_('date', $item->period_end, Text::_('DATE_FORMAT_LC4')) : '-'; ?>
                            </td>

                            <td class="d-none d-md-table-cell">
                                <?php echo $this->escape($item->period_title_de); ?>
                            </td>

                            <td class="d-none d-md-table-cell">
                                <?php echo $this->escape($item->period_title_it); ?>
                            </td>

                            <td class="d-none d-lg-table-cell">
                                <?php echo $this->escape($item->period_title_en); ?>
                            </td>

                            <td class="d-none d-xl-table-cell">
                                <?php echo $this->escape($item->period_title_fr); ?>
                            </td>

                            <td class="d-none d-xl-table-cell">
                                <?php echo $this->escape($item->period_title_es); ?>
                            </td>

                            <td class="d-none d-md-table-cell">
                                <?php echo $item->id; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <input type="hidden" name="task" value=""/>
                <input type="hidden" name="boxchecked" value="0"/>
                <input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
