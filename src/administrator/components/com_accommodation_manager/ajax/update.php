<?php
define('_JEXEC', 1);
define('JPATH_BASE', $_SERVER['DOCUMENT_ROOT']);

require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

use Joomla\CMS\Factory;

$db = Factory::getDbo();

$toUpdate = $_POST['update'];

// Security check to prevent direct access
if (empty($toUpdate) || strpos($_SERVER['HTTP_REFERER'], 'administrator/index.php?option=com_accommodation_manager&view=managerrates') === false) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

// Fetch current rates to back up
$query3 = $db->getQuery(true)
             ->select('*')
             ->from($db->quoteName('#__accommodation_manager_rates'));
$results = $db->setQuery($query3)->loadAssocList();
file_put_contents("bck-" . date("Y-m-d-H-i-s") . ".txt", serialize($results));

// Delete existing rates
$query = $db->getQuery(true)
            ->delete($db->quoteName('#__accommodation_manager_rates'));
$db->setQuery($query)->execute();

// Reinsert updated rates
$i = 0;
$createdBy = 413;  // Example creator ID

foreach ($toUpdate as $period_id => $period) {
    foreach ($period as $room_id => $room) {
        foreach ($room as $typology_id => $rate) {
            if ($rate !== '') {
                $query2 = $db->getQuery(true);
                $columns = ['ordering', 'state', 'checked_out', 'created_by', 'room_id', 'period_id', 'typology_id', 'rate'];
                $values = [$i, 1, 0, $createdBy, $room_id, $period_id, $typology_id, $db->quote($rate)];
                $query2->insert($db->quoteName('#__accommodation_manager_rates'))
                       ->columns($db->quoteName($columns))
                       ->values(implode(',', $values));
                $db->setQuery($query2)->execute();
                $i++;
            }
        }
    }
}

// Restore from backup if requested
if (!empty($_REQUEST['bck'])) {
    $results = unserialize(file_get_contents("bck-" . $_REQUEST['bck'] . ".txt"));
    foreach ($results as $r) {
        $query3 = $db->getQuery(true);
        $columns = ['ordering', 'state', 'checked_out', 'created_by', 'room_id', 'period_id', 'typology_id', 'rate'];
        $values = [$r['ordering'], $r['state'], $r['checked_out'], $r['created_by'], $r['room_id'], $r['period_id'], $r['typology_id'], $r['rate']];
        $query3->insert($db->quoteName('#__accommodation_manager_rates'))
               ->columns($db->quoteName($columns))
               ->values(implode(',', $values));
        $db->setQuery($query3)->execute();
    }
}

// Redirect back to the referring page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;