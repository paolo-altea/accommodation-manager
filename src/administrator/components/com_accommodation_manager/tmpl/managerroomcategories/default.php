<?php
/**
 * @version    CVS: 2.0.1
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2019. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Administrator\Helper\Accommodation_managerHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

HTMLHelper::_('bootstrap.tooltip');
$enabledLanguages = Accommodation_managerHelper::getEnabledLanguages();
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
    $saveOrderingUrl = 'index.php?option=com_accommodation_manager&task=managerroomcategories.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
    HTMLHelper::_('draggablelist.draggable');
}
?>

<form action="<?php echo Route::_('index.php?option=com_accommodation_manager&view=managerroomcategories'); ?>" method="post"
      name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

                <div class="clearfix"></div>
                <table class="table table-striped" id="roommanagercategoryList">
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
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_ACCOMMODATION_MANAGER_MANAGERROOMCATEGORIES_ROOM_CATEGORY_TITLE', 'a.room_category_title', $listDirn, $listOrder); ?>
                        </th>
                        <?php foreach ($enabledLanguages as $lang) : ?>
                        <th scope="col" class="d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', Text::sprintf('COM_ACCOMMODATION_MANAGER_NAME_LANG', strtoupper($lang)), 'a.room_category_name_' . $lang, $listDirn, $listOrder); ?>
                        </th>
                        <?php endforeach; ?>
                        <th scope="col">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_ACCOMMODATION_MANAGER_MANAGERROOMCATEGORIES_ROOM_CATEGORY_PARENT', 'a.room_category_parent', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" class="w-5 d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_FIELD_ID_LABEL', 'a.id', $listDirn, $listOrder); ?>
                        </th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <td colspan="<?php echo 6 + count($enabledLanguages); ?>">
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
                                    <input type="text" name="order[]" size="5" value="<?php echo (int) $item->ordering; ?>" class="width-20 text-area-order hidden">
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>

                            <td class="text-center">
                                <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'managerroomcategories.', $canChange, 'cb'); ?>
                            </td>

                            <td>
                                <?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
                                    <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'managerroomcategories.', $canCheckin); ?>
                                <?php endif; ?>
                                <?php if ($canEdit) : ?>
                                    <a href="<?php echo Route::_('index.php?option=com_accommodation_manager&task=roommanagercategory.edit&id=' . (int) $item->id); ?>">
                                        <?php echo $this->escape($item->room_category_title); ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo $this->escape($item->room_category_title); ?>
                                <?php endif; ?>
                            </td>

                            <?php foreach ($enabledLanguages as $lang) : ?>
                            <td class="d-none d-md-table-cell">
                                <?php
                                $nameField = 'room_category_name_' . $lang;
                                echo $this->escape($item->$nameField ?? '');
                                ?>
                            </td>
                            <?php endforeach; ?>

                            <td>
                                <?php if (empty($item->room_category_parent) || $item->room_category_parent === '0') : ?>
                                    <span class="text-muted"><?php echo Text::_('COM_ACCOMMODATION_MANAGER_NO_PARENT'); ?></span>
                                <?php else : ?>
                                    <?php echo $this->escape($item->room_category_parent); ?>
                                <?php endif; ?>
                            </td>

                            <td class="d-none d-md-table-cell">
                                <?php echo (int) $item->id; ?>
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
