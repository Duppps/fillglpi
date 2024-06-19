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

   Html::back();
} else if (isset($_POST["update"])) {
   echo "update";

   $obj->check($_POST['id'], UPDATE, $_POST);
   $obj->update($_POST);
   
   Html::back();
} else {
   $withtemplate = (isset($_GET['withtemplate']) ? $_GET['withtemplate'] : "");
   $id = isset($_GET['id']) ? $_GET['id'] : -1;

   BatteryModel::displayFullPageForItem($id, ["tools", BatteryModel::class], [
      'withtemplate' => $withtemplate,
      'formoptions'  => "data-track-changes=true",
   ]);
}