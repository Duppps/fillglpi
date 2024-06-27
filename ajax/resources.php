<?php
use GlpiPlugin\Cotrisoja\Sql;

$AJAX_INCLUDE = 1;

include('../../../inc/includes.php');

header("Content-Type: application/json; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

if (isset($_GET['item'])) {
    $items = Sql::getValuesByID($_GET['item'], 'glpi_reservationitems');

    foreach ($items as $item) {
        $idItem = $item['id'];
        $itemID = $item['items_id'];
        $tableItem = getTableForItemType($item['itemtype']);
    }
    
    $resources['Item'] = Sql::getSpecificField('name', $tableItem, $itemID, 'id');

    $resourcesData = Sql::getValuesByID($idItem, 'glpi_plugin_cotrisoja_resources', 'reservationitems_id');

    foreach ($resourcesData as $data) {
        $resources['resources'][] = [
            'id'                    =>  $data['id'],
            'name'                  =>  $data['name'],
            'reservationitems_id'   =>  $data['reservationitems_id']
        ];
    }

    header('Content-Type: application/json');

    echo json_encode($resources);
}