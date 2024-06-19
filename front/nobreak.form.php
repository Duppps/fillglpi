<?php

use GlpiPlugin\Cotrisoja\Nobreak;
use GlpiPlugin\Cotrisoja\Battery;

include ("../../../inc/includes.php");

$plugin = new Plugin();
$obj = new Nobreak();

if (!$plugin->isInstalled('cotrisoja') || !$plugin->isActivated('cotrisoja')) {
   Html::displayNotFoundError();
}

Session::checkLoginUser();


if (isset($_POST['add'])) {
   $obj->check(-1, CREATE, $_POST);
   $obj->add($_POST);

   Html::back();   
} else if (isset($_POST["update"])) {
   $obj->check($_POST['id'], UPDATE, $_POST);
   $obj->update($_POST);

   Html::back();
} else if (isset($_POST["delete"])) {
   $where = ' plugin_cotrisoja_nobreaks_id = '.$_POST['id'];
   Battery::out($where);

   $obj->check($_POST['id'], DELETE, $_POST);
   $obj->delete($_POST);

   Html::back();
} else {
   $withtemplate = (isset($_GET['withtemplate']) ? $_GET['withtemplate'] : "");
   $id = (isset($_GET['id']) ? $_GET['id'] : -1);

   Nobreak::displayFullPageForItem($id, ["assets", Nobreak::class], [
      'withtemplate' => $withtemplate,
      'formoptions'  => "data-track-changes=true",
   ]);
}
