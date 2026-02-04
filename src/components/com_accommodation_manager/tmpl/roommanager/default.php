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
use \Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

$canEdit = Factory::getUser()->authorise('core.edit', 'com_accommodation_manager.' . $this->item->id);

if (!$canEdit && Factory::getUser()->authorise('core.edit.own', 'com_accommodation_manager' . $this->item->id))
{
	$canEdit = Factory::getUser()->id == $this->item->created_by;
}
?>

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_NAME'); ?></th>
			<td><?php echo $this->item->room_name; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_CATEGORY'); ?></th>
			<td><?php echo $this->item->room_category; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_CODE'); ?></th>
			<td><?php echo $this->item->room_code; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_SURFACE'); ?></th>
			<td><?php echo $this->item->room_surface; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_PEOPLE'); ?></th>
			<td><?php echo $this->item->room_people; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_TITLE_DE'); ?></th>
			<td><?php echo $this->item->room_title_de; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_TITLE_IT'); ?></th>
			<td><?php echo $this->item->room_title_it; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_TITLE_EN'); ?></th>
			<td><?php echo $this->item->room_title_en; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_TITLE_FR'); ?></th>
			<td><?php echo $this->item->room_title_fr; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_TITLE_ES'); ?></th>
			<td><?php echo $this->item->room_title_es; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_INTRO_DE'); ?></th>
			<td><?php echo nl2br($this->item->room_intro_de); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_INTRO_IT'); ?></th>
			<td><?php echo nl2br($this->item->room_intro_it); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_INTRO_EN'); ?></th>
			<td><?php echo nl2br($this->item->room_intro_en); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_INTRO_FR'); ?></th>
			<td><?php echo nl2br($this->item->room_intro_fr); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_INTRO_ES'); ?></th>
			<td><?php echo nl2br($this->item->room_intro_es); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_DESCRIPTION_DE'); ?></th>
			<td><?php echo nl2br($this->item->room_description_de); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_DESCRIPTION_IT'); ?></th>
			<td><?php echo nl2br($this->item->room_description_it); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_DESCRIPTION_EN'); ?></th>
			<td><?php echo nl2br($this->item->room_description_en); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_DESCRIPTION_FR'); ?></th>
			<td><?php echo nl2br($this->item->room_description_fr); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_DESCRIPTION_ES'); ?></th>
			<td><?php echo nl2br($this->item->room_description_es); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_FLOOR_PLAN'); ?></th>
			<td><?php echo $this->item->room_floor_plan; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_THUMBNAIL'); ?></th>
			<td><?php echo $this->item->room_thumbnail; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_GALLERY'); ?></th>
			<td><?php echo $this->item->room_gallery; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_VIDEO'); ?></th>
			<td><?php echo $this->item->room_video; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_ROOMMANAGER_ROOM_PANO'); ?></th>
			<td><?php echo $this->item->room_pano; ?></td>
		</tr>

	</table>

</div>

<?php $canCheckin = Factory::getUser()->authorise('core.manage', 'com_accommodation_manager.' . $this->item->id) || $this->item->checked_out == Factory::getUser()->id; ?>
	<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_accommodation_manager&task=roommanager.edit&id='.$this->item->id); ?>"><?php echo Text::_("COM_ACCOMMODATION_MANAGER_EDIT_ITEM"); ?></a>
	<?php elseif($canCheckin && $this->item->checked_out > 0) : ?>
	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_accommodation_manager&task=roommanager.checkin&id=' . $this->item->id .'&'. Session::getFormToken() .'=1'); ?>"><?php echo Text::_("JLIB_HTML_CHECKIN"); ?></a>

<?php endif; ?>

<?php if (Factory::getUser()->authorise('core.delete','com_accommodation_manager.roommanager.'.$this->item->id)) : ?>

	<a class="btn btn-danger" rel="noopener noreferrer" href="#deleteModal" role="button" data-bs-toggle="modal">
		<?php echo Text::_("COM_ACCOMMODATION_MANAGER_DELETE_ITEM"); ?>
	</a>

	<?php echo HTMLHelper::_(
                                    'bootstrap.renderModal',
                                    'deleteModal',
                                    array(
                                        'title'  => Text::_('COM_ACCOMMODATION_MANAGER_DELETE_ITEM'),
                                        'height' => '50%',
                                        'width'  => '20%',
                                        
                                        'modalWidth'  => '50',
                                        'bodyHeight'  => '100',
                                        'footer' => '<button class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button><a href="' . Route::_('index.php?option=com_accommodation_manager&task=roommanager.remove&id=' . $this->item->id, false, 2) .'" class="btn btn-danger">' . Text::_('COM_ACCOMMODATION_MANAGER_DELETE_ITEM') .'</a>'
                                    ),
                                    Text::sprintf('COM_ACCOMMODATION_MANAGER_DELETE_CONFIRM', $this->item->id)
                                ); ?>

<?php endif; ?>