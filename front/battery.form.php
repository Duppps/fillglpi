<?php

use GlpiPlugin\Cotrisoja\Battery;

include ("../../../inc/includes.php");

$plugin = new Plugin();
$obj = new Battery();

if (!$plugin->isInstalled('cotrisoja') || !$plugin->isActivated('cotrisoja')) {
   Html::displayNotFoundError();
}

Session::checkLoginUser();


if (isset($_POST['add'])) {
   $obj->check(-1, CREATE, $_POST);

   for ($i=0; $i<$_POST['quantity']; $i++) {
      $obj->add($_POST);
   }

   Html::back(); 

} else if (isset($_POST["update"])) {
   echo "update";

} else {
   $withtemplate = (isset($_GET['withtemplate']) ? $_GET['withtemplate'] : "");
   $id = (isset($_GET['id']) ? $_GET['id'] : -1);

   Battery::displayFullPageForItem($id, ["assets", Battery::class], [
      'withtemplate' => $withtemplate,
      'formoptions'  => "data-track-changes=true",
   ]);
}
