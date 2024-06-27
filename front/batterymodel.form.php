<?php

use GlpiPlugin\Cotrisoja\BatteryModel;

include ("../../../inc/includes.php");

$plugin = new Plugin();
$obj = new BatteryModel();

if (!$plugin->isInstalled('cotrisoja') || !$plugin->isActivated('cotrisoja')) {
   Html::displayNotFoundError();
}

Session::checkLoginUser();

if (isset($_POST['add'])) {
   $fields = [
      'name' => $_POST['name'],
      'brand' => $_POST['brand']
   ];

   $obj->check(-1, CREATE, $_POST);
   $obj->add($_POST);

   Html::redirect($obj->getLinkURL());
} else if (isset($_POST["update"])) {
   $obj->check($_POST['id'], UPDATE, $_POST);
   $obj->update($_POST);
   
   Html::redirect($obj->getLinkURL());
} else if (isset($_POST["delete"])) {
   $obj->check($_POST['id'], PURGE, $_POST);
   $obj->delete($_POST);

   $obj->redirectToList();
} else {
   $withtemplate = (isset($_GET['withtemplate']) ? $_GET['withtemplate'] : "");
   $id = isset($_GET['id']) ? $_GET['id'] : -1;

   BatteryModel::displayFullPageForItem($id, null, [
      'withtemplate' => $withtemplate,
      'formoptions'  => "data-track-changes=true",
   ]);
}