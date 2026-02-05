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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

$app = Factory::getApplication();
$user = $app->getIdentity();
?>

<?php if (!$this->hasCategories) : ?>
<div class="alert alert-warning">
    <h4 class="alert-heading"><?php echo Text::_('COM_ACCOMMODATION_MANAGER_NO_CATEGORIES_TITLE'); ?></h4>
    <p><?php echo Text::_('COM_ACCOMMODATION_MANAGER_NO_CATEGORIES_DESC'); ?></p>
    <hr>
    <a href="<?php echo Route::_('index.php?option=com_accommodation_manager&task=roommanagercategory.add'); ?>" class="btn btn-primary">
        <span class="icon-new" aria-hidden="true"></span>
        <?php echo Text::_('COM_ACCOMMODATION_MANAGER_CREATE_CATEGORY'); ?>
    </a>
    <a href="<?php echo Route::_('index.php?option=com_accommodation_manager&view=managerroomcategories'); ?>" class="btn btn-secondary">
        <?php echo Text::_('COM_ACCOMMODATION_MANAGER_VIEW_CATEGORIES'); ?>
    </a>
</div>
<?php else : ?>

<form
    action="<?php echo Route::_('index.php?option=com_accommodation_manager&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" enctype="multipart/form-data" name="adminForm" id="roommanager-form" class="form-validate">

    <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'base', 'recall' => true, 'breakpoint' => 768]); ?>

    <!-- Tab Dati Base -->
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'base', Text::_('COM_ACCOMMODATION_MANAGER_TAB_BASE')); ?>
    <div class="row">
        <div class="col-lg-9">
            <?php echo $this->form->renderField('room_name'); ?>
            <?php echo $this->form->renderField('room_category'); ?>
            <?php echo $this->form->renderField('room_code'); ?>
            <?php echo $this->form->renderField('room_surface'); ?>
            <?php echo $this->form->renderField('room_people'); ?>
            <?php echo $this->form->renderField('room_price_from'); ?>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <?php echo $this->form->renderField('state'); ?>
                    <?php echo $this->form->renderField('id'); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <!-- Tab Contenuti DE -->
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'content_de', Text::_('COM_ACCOMMODATION_MANAGER_TAB_CONTENT_DE')); ?>
    <div class="row">
        <div class="col-12">
            <?php echo $this->form->renderField('room_title_de'); ?>
            <?php echo $this->form->renderField('room_intro_de'); ?>
            <?php echo $this->form->renderField('room_description_de'); ?>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <!-- Tab Contenuti IT -->
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'content_it', Text::_('COM_ACCOMMODATION_MANAGER_TAB_CONTENT_IT')); ?>
    <div class="row">
        <div class="col-12">
            <?php echo $this->form->renderField('room_title_it'); ?>
            <?php echo $this->form->renderField('room_intro_it'); ?>
            <?php echo $this->form->renderField('room_description_it'); ?>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <!-- Tab Contenuti EN -->
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'content_en', Text::_('COM_ACCOMMODATION_MANAGER_TAB_CONTENT_EN')); ?>
    <div class="row">
        <div class="col-12">
            <?php echo $this->form->renderField('room_title_en'); ?>
            <?php echo $this->form->renderField('room_intro_en'); ?>
            <?php echo $this->form->renderField('room_description_en'); ?>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <!-- Tab Contenuti FR -->
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'content_fr', Text::_('COM_ACCOMMODATION_MANAGER_TAB_CONTENT_FR')); ?>
    <div class="row">
        <div class="col-12">
            <?php echo $this->form->renderField('room_title_fr'); ?>
            <?php echo $this->form->renderField('room_intro_fr'); ?>
            <?php echo $this->form->renderField('room_description_fr'); ?>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <!-- Tab Contenuti ES -->
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'content_es', Text::_('COM_ACCOMMODATION_MANAGER_TAB_CONTENT_ES')); ?>
    <div class="row">
        <div class="col-12">
            <?php echo $this->form->renderField('room_title_es'); ?>
            <?php echo $this->form->renderField('room_intro_es'); ?>
            <?php echo $this->form->renderField('room_description_es'); ?>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <!-- Tab Media -->
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'media', Text::_('COM_ACCOMMODATION_MANAGER_TAB_MEDIA')); ?>
    <div class="row">
        <div class="col-lg-6">
            <h4><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FIELDSET_FLOOR_PLAN'); ?></h4>
            <?php echo $this->form->renderField('room_floor_plan'); ?>
            <?php echo $this->form->renderField('room_floor_plan_alt_de'); ?>
            <?php echo $this->form->renderField('room_floor_plan_alt_it'); ?>
            <?php echo $this->form->renderField('room_floor_plan_alt_en'); ?>
            <?php echo $this->form->renderField('room_floor_plan_alt_fr'); ?>
            <?php echo $this->form->renderField('room_floor_plan_alt_es'); ?>
        </div>
        <div class="col-lg-6">
            <h4><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FIELDSET_THUMBNAIL'); ?></h4>
            <?php echo $this->form->renderField('room_thumbnail'); ?>
            <?php echo $this->form->renderField('room_thumbnail_alt_de'); ?>
            <?php echo $this->form->renderField('room_thumbnail_alt_it'); ?>
            <?php echo $this->form->renderField('room_thumbnail_alt_en'); ?>
            <?php echo $this->form->renderField('room_thumbnail_alt_fr'); ?>
            <?php echo $this->form->renderField('room_thumbnail_alt_es'); ?>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <h4><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FIELDSET_VIDEO'); ?></h4>
            <?php echo $this->form->renderField('room_video'); ?>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <!-- Tab Gallery -->
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'gallery', Text::_('COM_ACCOMMODATION_MANAGER_TAB_GALLERY')); ?>
    <div class="row">
        <div class="col-12">
            <?php echo $this->form->renderField('room_gallery'); ?>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <!-- Tab Permessi -->
    <?php if ($user->authorise('core.admin', 'com_accommodation_manager')) : ?>
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_('JGLOBAL_ACTION_PERMISSIONS_LABEL')); ?>
    <div class="row">
        <div class="col-12">
            <?php echo $this->form->getInput('rules'); ?>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>
    <?php endif; ?>

    <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
    <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
    <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
    <input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
    <?php echo $this->form->renderField('created_by'); ?>

    <input type="hidden" name="task" value=""/>
    <?php echo HTMLHelper::_('form.token'); ?>

</form>
<?php endif; ?>
