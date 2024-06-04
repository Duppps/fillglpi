<?php

use GlpiPlugin\Cotrisoja\BD;

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

echo $result;

