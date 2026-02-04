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

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;


HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
?>

<form
	action="<?php echo Route::_('index.php?option=com_accommodation_manager&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="roommanager-form" class="form-validate form-horizontal">

	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'roommanager')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'roommanager', Text::_('COM_ACCOMMODATION_MANAGER_TAB_ROOMMANAGER', true)); ?>
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FIELDSET_ROOMMANAGER'); ?></legend>
				<?php echo $this->form->renderField('room_name'); ?>
				<?php echo $this->form->renderField('room_category'); ?>
				<?php echo $this->form->renderField('room_code'); ?>
				<?php echo $this->form->renderField('room_surface'); ?>
				<?php echo $this->form->renderField('room_people'); ?>
				<?php echo $this->form->renderField('room_title_de'); ?>
				<?php echo $this->form->renderField('room_title_it'); ?>
				<?php echo $this->form->renderField('room_title_en'); ?>
				<?php echo $this->form->renderField('room_title_fr'); ?>
				<?php echo $this->form->renderField('room_title_es'); ?>
				<?php echo $this->form->renderField('room_intro_de'); ?>
				<?php echo $this->form->renderField('room_intro_it'); ?>
				<?php echo $this->form->renderField('room_intro_en'); ?>
				<?php echo $this->form->renderField('room_intro_fr'); ?>
				<?php echo $this->form->renderField('room_intro_es'); ?>
				<?php echo $this->form->renderField('room_description_de'); ?>
				<?php echo $this->form->renderField('room_description_it'); ?>
				<?php echo $this->form->renderField('room_description_en'); ?>
				<?php echo $this->form->renderField('room_description_fr'); ?>
				<?php echo $this->form->renderField('room_description_es'); ?>
				<?php echo $this->form->renderField('room_floor_plan'); ?>
				<?php echo $this->form->renderField('room_thumbnail'); ?>
				<?php echo $this->form->renderField('room_gallery'); ?>
				<?php echo $this->form->renderField('room_video'); ?>
				<?php echo $this->form->renderField('room_pano'); ?>
				<?php if ($this->state->params->get('save_history', 1)) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
					</div>
				<?php endif; ?>
			</fieldset>
		</div>
	</div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
	<?php echo $this->form->renderField('created_by'); ?>

	<?php if (Factory::getUser()->authorise('core.admin','accommodation_manager')) : ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_('JGLOBAL_ACTION_PERMISSIONS_LABEL', true)); ?>
		<?php echo $this->form->getInput('rules'); ?>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
<?php endif; ?>
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
