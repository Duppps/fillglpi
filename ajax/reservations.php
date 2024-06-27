<?php
use GlpiPlugin\Cotrisoja\Sql;

$AJAX_INCLUDE = 1;

include('../../../inc/includes.php');

header("Content-Type: application/json; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

if (isset($_GET['id'])) {
    $data = Sql::getReservationInfo($_GET['id']);
    echo json_encode($data);
} else if (isset($_GET['byList'])) {
    echo json_encode(Sql::getOpenReservationInfo($_GET['byList']));
}