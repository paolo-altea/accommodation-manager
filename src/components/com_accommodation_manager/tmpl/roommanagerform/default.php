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
use \Accomodationmanager\Component\Accommodation_manager\Site\Helper\Accommodation_managerHelper;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_accommodation_manager', JPATH_SITE);

$user    = Factory::getUser();
$canEdit = Accommodation_managerHelper::canUserEdit($this->item, $user);


?>

<div class="roommanager-edit front-end-edit">
	<?php if (!$canEdit) : ?>
		<h3>
		<?php throw new \Exception(Text::_('COM_ACCOMMODATION_MANAGER_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo Text::sprintf('COM_ACCOMMODATION_MANAGER_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
		<?php else: ?>
			<h1><?php echo Text::_('COM_ACCOMMODATION_MANAGER_ADD_ITEM_TITLE'); ?></h1>
		<?php endif; ?>

		<form id="form-roommanager"
			  action="<?php echo Route::_('index.php?option=com_accommodation_manager&task=roommanagerform.save'); ?>"
			  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
	<input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />

	<input type="hidden" name="jform[ordering]" value="<?php echo isset($this->item->ordering) ? $this->item->ordering : ''; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo isset($this->item->state) ? $this->item->state : ''; ?>" />

	<input type="hidden" name="jform[checked_out]" value="<?php echo isset($this->item->checked_out) ? $this->item->checked_out : ''; ?>" />

	<input type="hidden" name="jform[checked_out_time]" value="<?php echo isset($this->item->checked_out_time) ? $this->item->checked_out_time : ''; ?>" />

				<?php echo $this->form->getInput('created_by'); ?>
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'roommanager')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'roommanager', Text::_('COM_ACCOMMODATION_MANAGER_TAB_ROOMMANAGER', true)); ?>
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

	<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<span class="fas fa-check" aria-hidden="true"></span>
							<?php echo Text::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn btn-danger"
					   href="<?php echo Route::_('index.php?option=com_accommodation_manager&task=roommanagerform.cancel'); ?>"
					   title="<?php echo Text::_('JCANCEL'); ?>">
					   <span class="fas fa-times" aria-hidden="true"></span>
						<?php echo Text::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_accommodation_manager"/>
			<input type="hidden" name="task"
				   value="roommanagerform.save"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>
