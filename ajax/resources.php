<?php

use GlpiPlugin\Fillglpi\Sql;
use GlpiPlugin\Fillglpi\Resource;

$AJAX_INCLUDE = 1;

include('../../../inc/includes.php');

header("Content-Type: application/json; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

if (isset($_GET['item']) && isset($_GET['dateBegin']) && isset($_GET['dateEnd'])) {
    $items = Sql::getValuesByID($_GET['item'], 'glpi_reservationitems');

    foreach ($items as $item) {
        $idItem = $item['id'];
        $itemID = $item['items_id'];
        $tableItem = getTableForItemType($item['itemtype']);
    }

    $resources['Item'] = Sql::getSpecificField('name', $tableItem, $itemID, 'id');

    $resourcesData = Resource::getResourceByItemTypeAndCheckAvailability($idItem, $_GET['dateBegin'], $_GET['dateEnd']);

    foreach ($resourcesData as &$data) {
        $resources['resources'][] = [
            'id'                    =>  $data['id'],
            'name'                  =>  $data['name'],
            'for'                   =>  $idItem,
            'availability'          =>  $data['availability']
        ];
    }

    header('Content-Type: application/json');

    echo json_encode($resources);
} else if ($_GET['resourceCalendarID']) {
    $items = Sql::getReservationsResources($_GET['resourceCalendarID']);

    header('Content-Type: application/json');
    echo json_encode($items);
}
