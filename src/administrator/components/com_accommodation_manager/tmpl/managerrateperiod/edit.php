<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Accomodationmanager\Component\Accommodation_manager\Administrator\Helper\Accommodation_managerHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');

// Add language string for JS
$this->document->addScriptOptions('com_accommodation_manager', [
    'errorPeriodEndBeforeStart' => Text::_('COM_ACCOMMODATION_MANAGER_ERROR_PERIOD_END_BEFORE_START')
]);

$enabledLanguages = Accommodation_managerHelper::getEnabledLanguages();
?>

<form
    action="<?php echo Route::_('index.php?option=com_accommodation_manager&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" enctype="multipart/form-data" name="adminForm" id="managerrateperiod-form" class="form-validate">

    <div class="row">
        <div class="col-lg-9">
            <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'general', 'recall' => true, 'breakpoint' => 768]); ?>

            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_ACCOMMODATION_MANAGER_TAB_MANAGERRATEPERIOD')); ?>

            <!-- Date Range -->
            <div class="row">
                <div class="col-md-6">
                    <fieldset class="options-form">
                        <?php echo $this->form->renderField('period_start'); ?>
                    </fieldset>
                </div>
                <div class="col-md-6">
                    <fieldset class="options-form">
                        <?php echo $this->form->renderField('period_end'); ?>
                    </fieldset>
                </div>
            </div>

            <!-- Language Titles -->
            <div class="row">
                <?php foreach ($enabledLanguages as $index => $lang) : ?>
                <div class="col-md-6">
                    <fieldset class="options-form">
                        <legend><?php echo Text::_('COM_ACCOMMODATION_MANAGER_MANAGERRATEPERIODS_PERIOD_TITLE_' . strtoupper($lang)); ?></legend>
                        <?php echo $this->form->renderField('period_title_' . $lang); ?>
                    </fieldset>
                </div>
                <?php if (($index + 1) % 2 === 0 && $index < count($enabledLanguages) - 1) : ?>
            </div>
            <div class="row">
                <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <?php if ($this->state->params->get('save_history', 1)) : ?>
            <div class="row">
                <div class="col-12">
                    <?php echo $this->form->renderField('version_note'); ?>
                </div>
            </div>
            <?php endif; ?>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <?php echo $this->form->renderField('state'); ?>
                    <?php echo $this->form->renderField('created_by'); ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
    <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
    <input type="hidden" name="jform[checked_out]" value="<?php echo isset($this->item->checked_out) ? $this->item->checked_out : ''; ?>" />
    <input type="hidden" name="jform[checked_out_time]" value="<?php echo isset($this->item->checked_out_time) ? $this->item->checked_out_time : ''; ?>" />

    <input type="hidden" name="task" value=""/>
    <?php echo HTMLHelper::_('form.token'); ?>

</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('managerrateperiod-form');

    form.addEventListener('submit', function(e) {
        var task = form.querySelector('input[name="task"]').value;
        if (task === 'managerrateperiod.cancel') {
            return;
        }

        var startField = document.getElementById('jform_period_start');
        var endField = document.getElementById('jform_period_end');

        if (startField && endField && startField.value && endField.value) {
            var startDate = new Date(startField.value);
            var endDate = new Date(endField.value);

            if (endDate < startDate) {
                e.preventDefault();
                e.stopPropagation();
                var options = Joomla.getOptions('com_accommodation_manager');
                Joomla.renderMessages({
                    'error': [options.errorPeriodEndBeforeStart]
                });
                endField.focus();
                return false;
            }
        }
    });
});
</script>
