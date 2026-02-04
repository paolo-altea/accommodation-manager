<?php

/*
 * UPDATED BY MATTHIAS STAMPFER, ALTEA
*/

/**
 * @version     1.0.0
 * @package     com_accommodation_manager
 * @copyright   Copyright (C) 2015. Tutti i diritti riservati.
 * @license     GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 * @author      Altea Software Srl <stampfer@altea.it> - http://www.altea.it
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_accommodation_manager/assets/css/accommodation_manager.css');

$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_accommodation_manager');
$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_accommodation_manager&task=managerrates.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'managerrateList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}

	jQuery(document).ready(function () {
		jQuery('#clear-search-button').on('click', function () {
			jQuery('#filter_search').val('');
			jQuery('#adminForm').submit();
		});
	});
</script>

<?php
//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar)) {
    $this->sidebar .= $this->extra_sidebar;
}
?>

<div style="clear:both;"></div>
<div id="j-sidebar-containera" class="span2"></div>
<div id="j-main-containera" class="span10">

    <?php
    
    $lang = (string) substr(JFactory::getLanguage()->getTag(),0,2);
    
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array('id', 'period_start', 'period_end', 'period_title_de', 'period_title_it', 'period_title_en')));
    $query->from($db->quoteName('#__accommodation_manager_rate_periods'));
    $query->where($db->quoteName('state') . ' = 1');
    $query->order('period_start ASC');
    $db->setQuery($query);
    $periods = $db->loadObjectList();
    
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array('id', 'room_name', 'room_code')));
    $query->from($db->quoteName('#__accommodation_manager_rooms'));
    $query->where($db->quoteName('state') . ' = 1');
    $query->order('ordering ASC');
    $db->setQuery($query);
    $rooms = $db->loadObjectList();
    
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array('id', 'rate_typology_de', 'rate_typology_it', 'rate_typology_en')));
    $query->from($db->quoteName('#__accommodation_manager_rate_typologies'));
    $query->where($db->quoteName('state') . ' = 1');
    $query->order('ordering ASC');
    $db->setQuery($query);
    $types = $db->loadObjectList();
    
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array('id', 'room_id', 'period_id', 'typology_id', 'rate')));
    $query->from($db->quoteName('#__accommodation_manager_rates'));
    $query->where($db->quoteName('state') . ' = 1');
    $query->order('ordering ASC');
    $db->setQuery($query);
    $rates = $db->loadObjectList();
    
    $outputRates = array();
    foreach($rates as $rate) {
        $outputRates[$rate->period_id][$rate->room_id][$rate->typology_id] = $rate->rate;
    }
    
    ?>
    
    
    <div class="rate-table-box">
        <form action="components/com_accommodation_manager/ajax/update.php" method="post">
            <div class="addbuttons"><a href="index.php?option=com_accommodation_manager&view=managerrateperiod&layout=edit" class="btn btn-small btn-info"><span class="icon-new icon-white"></span> Neuen Zeitraum hinzufügen</a></div>
            <div class="savebutton"><button class="btn btn-small btn-success"><span class="icon-pencil icon-white"></span> Save the rates</button></div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo JText::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_MANAGERRATE_PERIOD_ID'); ?></th>
                    <th><?php echo JText::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_MANAGERRATEPERIOD_PERIOD_START'); ?></th>
                    <th><?php echo JText::_('COM_ACCOMMODATION_MANAGER_FORM_LBL_MANAGERRATEPERIOD_PERIOD_END'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($periods as $period) { ?>
                <?php
                
                $pTitle = 'period_title_' . $lang; 
                
                ?>
                <tr>
                    <td class="tit"><?php echo $period->$pTitle; ?></td>
                    <td class="date"><input type="text" readonly value="<?php echo date('d.m.Y', strtotime($period->period_start)); ?>"></td>
                    <td class="date"><input type="text" readonly value="<?php echo date('d.m.Y',strtotime($period->period_end)); ?>"></td>
                    <td class="rates">
                        <table class="table table-striped-negative">
                            <thead>
                                <tr>
                                    <th><?php echo JText::_('COM_ACCOMMODATION_MANAGER_MANAGERRATES_ROOM_ID'); ?></th>
                                    <?php
                                    
                                    foreach($types as $type) { 
                                        
                                        $tTitle = 'rate_typology_' . $lang; 
                                    
                                    ?>
                                    <th class="rate"><?php echo $type->$tTitle; ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($rooms as $room) { ?>
                                <tr>
                                    <td><?php echo $room->room_name; ?></td>
                                    <?php foreach($types as $type) { ?>
                                    <td class="rate"><input type="text" name="update[<?php echo $period->id; ?>][<?php echo $room->id; ?>][<?php echo $type->id; ?>]" value="<?php if(isset($outputRates[$period->id][$room->id][$type->id])) { echo $outputRates[$period->id][$room->id][$type->id]; } ?>"></td>
                                    <?php } ?>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
            <div class="addbuttons"><a href="index.php?option=com_accommodation_manager&view=managerrateperiod&layout=edit" class="btn btn-small btn-info"><span class="icon-new icon-white"></span> Neuen Zeitraum hinzufügen</a></div>
            <div class="savebutton"><button class="btn btn-small btn-success"><span class="icon-pencil icon-white"></span> Save the rates</button></div>
        </form>
    </div>		



</div>

<style>
    .table.table-striped { margin: 30px 0;}
    .table.table-striped .tit { min-width: 80px;}
    .table.table-striped td { vertical-align: middle;}
    .table.table-striped td.rates  { padding-left: 30px;}
    .rate-table-box { position: relative; box-sizing: border-box; padding:10px;}
    .table .date input { width: 120px; text-align: center;}
    .table .rate input { width: 80px; text-align: center; font-size: 11px;}
    .table .tit input { background: none; border: none; max-width: 130px; box-shadow: none; display: inline; width: auto;}
    .table-striped-negative  { margin: 0px; box-sizing: border-box;}
    .table-striped-negative th { font-size: 12px;}
    .savebutton { text-align:right;}
    .addbuttons { float: left;}
</style>
