<?php

use GlpiPlugin\Fillglpi\Resource;
use GlpiPlugin\Fillglpi\Sql;
use Glpi\Event;

include("../../../inc/includes.php");

$plugin = new Plugin();
$obj = new Resource();

if (!$plugin->isInstalled('fillglpi') || !$plugin->isActivated('fillglpi')) {
   Html::displayNotFoundError();
}

Session::checkLoginUser();

if (isset($_POST['add'])) {

   if (!isset($_POST['open_ticket'])) {
      unset($_POST['entities_id']);
   }

   if (!isset($_POST['include_quantity'])) {
      unset($_POST['stock']);
   }

   $obj->check(-1, CREATE, $_POST);
   $obj->add($_POST);

   foreach ($_POST as $key => $value) {
      if (strpos($key, 'item_id_') === 0) {
         Sql::insert('glpi_plugin_fillglpi_resources_reservationsitems', ['plugin_fillglpi_resources_id' => $obj->getID(), 'reservationitems_id' => $value]);
      }
   }

   Html::redirect($obj->getLinkURL());
} else if (isset($_POST["update"])) {

   if (!isset($_POST['open_ticket'])) {
      $_POST['entities_id'] = NULL;
   }

   if (!isset($_POST['include_quantity'])) {
      $_POST['stock'] = NULL;
   }

   Resource::removeFromResourcesReservationItems($_POST['id']);

   foreach ($_POST as $key => $value) {
      if (strpos($key, 'item_id_') === 0) {
         Sql::insert('glpi_plugin_fillglpi_resources_reservationsitems', ['plugin_fillglpi_resources_id' => $_POST['id'], 'reservationitems_id' => $value]);
      }
   }

   $obj->check($_POST['id'], UPDATE, $_POST);
   $obj->update($_POST);

   Html::redirect($obj->getLinkURL());
} else if (isset($_POST['delete'])) {
   $obj->check($_POST["id"], PURGE);

   if ($obj->delete($_POST)) {
      Event::log(
         $_POST["id"],
         Resource::class,
         4,
         "",
         //TRANS: %s is the user login
         sprintf(__('%s deletes an item'), $_SESSION["glpiname"])
      );
   }
   Resource::removeFromResourcesReservationItems($_POST['id']);

   $obj->redirectToList();
} else {
   $withtemplate = (isset($_GET['withtemplate']) ? $_GET['withtemplate'] : "");
   $id = isset($_GET['id']) ? $_GET['id'] : -1;

   Resource::displayFullPageForItem($id, null, [
      'withtemplate' => $withtemplate,
      'formoptions'  => "data-track-changes=true",
   ]);
}
