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

<form
    action="<?php echo Route::_('index.php?option=com_accommodation_manager&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" enctype="multipart/form-data" name="adminForm" id="roommanagercategory-form" class="form-validate">

    <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'base', 'recall' => true, 'breakpoint' => 768]); ?>

    <!-- Tab Dati Base -->
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'base', Text::_('COM_ACCOMMODATION_MANAGER_TAB_BASE')); ?>
    <div class="row">
        <div class="col-lg-9">
            <?php echo $this->form->renderField('room_category_title'); ?>
            <?php echo $this->form->renderField('room_category_parent'); ?>
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
            <?php echo $this->form->renderField('room_category_name_de'); ?>
            <?php echo $this->form->renderField('room_category_description_de'); ?>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <!-- Tab Contenuti IT -->
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'content_it', Text::_('COM_ACCOMMODATION_MANAGER_TAB_CONTENT_IT')); ?>
    <div class="row">
        <div class="col-12">
            <?php echo $this->form->renderField('room_category_name_it'); ?>
            <?php echo $this->form->renderField('room_category_description_it'); ?>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <!-- Tab Contenuti EN -->
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'content_en', Text::_('COM_ACCOMMODATION_MANAGER_TAB_CONTENT_EN')); ?>
    <div class="row">
        <div class="col-12">
            <?php echo $this->form->renderField('room_category_name_en'); ?>
            <?php echo $this->form->renderField('room_category_description_en'); ?>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <!-- Tab Contenuti FR -->
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'content_fr', Text::_('COM_ACCOMMODATION_MANAGER_TAB_CONTENT_FR')); ?>
    <div class="row">
        <div class="col-12">
            <?php echo $this->form->renderField('room_category_name_fr'); ?>
            <?php echo $this->form->renderField('room_category_description_fr'); ?>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <!-- Tab Contenuti ES -->
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'content_es', Text::_('COM_ACCOMMODATION_MANAGER_TAB_CONTENT_ES')); ?>
    <div class="row">
        <div class="col-12">
            <?php echo $this->form->renderField('room_category_name_es'); ?>
            <?php echo $this->form->renderField('room_category_description_es'); ?>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
    <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
    <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
    <input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
    <?php echo $this->form->renderField('created_by'); ?>

    <input type="hidden" name="task" value=""/>
    <?php echo HTMLHelper::_('form.token'); ?>

</form>
