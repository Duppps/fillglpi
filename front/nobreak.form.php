<?php

use GlpiPlugin\Cotrisoja\Nobreak;

include ("../../../inc/includes.php");

$plugin = new Plugin();
$obj = new Nobreak();

if (!$plugin->isInstalled('cotrisoja') || !$plugin->isActivated('cotrisoja')) {
   Html::displayNotFoundError();
}

Session::checkLoginUser();


if (isset($_POST['add'])) {
   unset($_POST['id']);  
   

   $obj->check(-1, CREATE, $_POST);
   $obj->add($_POST);

   Html::back();   

} else if (isset($_POST["update"])) {
   echo "update";

} else {
   $withtemplate = (isset($_GET['withtemplate']) ? $_GET['withtemplate'] : "");
   $id = (isset($_GET['id']) ? $_GET['id'] : -1);

   Nobreak::displayFullPageForItem($id, ["assets", Nobreak::class], [
      'withtemplate' => $withtemplate,
      'formoptions'  => "data-track-changes=true",
   ]);
}
