<?php

use GlpiPlugin\Fillglpi\BD;
use GlpiPlugin\Fillglpi\Sql;

$AJAX_INCLUDE = 1;

include('../../../inc/includes.php');

header("Content-Type: application/json; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

if (isset($_GET["entidades"])) {
    $currentEntityID = BD::getEntityByTicket($_GET["entidades"]);
    $where = 'WHERE id <> ' . $currentEntityID . ' AND entities_id IS NOT NULL';
    $entities = BD::getEntities($where);

    http_response_code(200);
    $result = json_encode($entities); 
} 

if (isset($_GET["getCurrentEntity"])) {
    $currentEntityID = BD::getEntityByTicket($_GET["getCurrentEntity"]);

    http_response_code(200);
    $result = json_encode($currentEntityID);
}

if (isset($_GET["getAllEntities"])) {
    $a = [];

    $entities = Sql::getAllDataWithFieldAndOperator('glpi_entities', 'level', '1', '>');

    foreach ($entities as $entity) {
        $a[] = [
            "id"    =>  $entity["id"],
            "name"  =>  $entity["name"]
        ];
    }

    http_response_code(200);
    $result = json_encode($a);
}

echo $result;

