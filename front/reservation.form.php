<?php

use GlpiPlugin\Fillglpi\Reservation;
use GlpiPlugin\Fillglpi\Resource;

include ("../../../inc/includes.php");

$plugin = new Plugin();
$obj = new Reservation();

if (!$plugin->isInstalled('fillglpi') || !$plugin->isActivated('fillglpi')) {
   Html::displayNotFoundError();
}

Session::checkLoginUser();

if (isset($_POST['add'])) {
    $_POST['reservations_id'] = $_GET['id'];

    $obj->check(-1, CREATE, $_POST);
    $obj->add($_POST);

    foreach ($_POST as $i => $key) {
        if (strpos($i, 'resource_id_') !== false) { 
            if (!Resource::create($key, $_POST['reservations_id'])) {
                Session::addMessageAfterRedirect(
                    __('Resource not avaible for this date'),
                    false,
                    ERROR
                );
            }          
        }
    } 
    
    $ri = new \ReservationItem();
    $ri->redirectToList();
} else if (isset($_POST["update"])) {
   
    Html::redirect($obj->getLinkURL());
} else {    
    $withtemplate = isset($_GET['withtemplate']) ? $_GET['withtemplate'] : "";
    $id = -1; 
    
    Reservation::displayFullPageForItem($id, null, [
        'idReservation' => $_GET['id']
    ]);
}