<?php

use GlpiPlugin\FillGlpi\Limpeza;
use GlpiPlugin\FillGlpi\BD;

include ("../../../inc/includes.php");

$plugin = new Plugin();
if (!$plugin->isInstalled('fillglpi') || !$plugin->isActivated('fillglpi')) {
   Html::displayNotFoundError();
}

Session::checkLoginUser();

$object = new Limpeza();
$conn = new BD;

if (isset($_POST['add'])) {
   if (isset($_POST['consumables']) && Session::haveRight(Consumable::$rightname, UPDATE)) {
      $idComputer = $_POST['computers_id'];
      $consumablesToAdd = [];

      $insumos = explode(";", $_POST['consumables']);   
      array_pop($insumos);  

      foreach ($insumos as $insumo) {
         if (!Consumable::isNew($insumo)) {
            Session::addMessageAfterRedirect(
               __('Sem insumos suficientes disponíveis'),
               true,
               ERROR
            );

            Html::back();
         }
      }
   }

   $object->check(-1, CREATE, $_POST);
   $newid = $object->add($_POST);

   foreach ($insumos as $insumo){
      $consumable = new Consumable();

      if (!$consumable->out($insumo, Limpeza::class, $newid)) {
         Session::addMessageAfterRedirect(
            __('Erro ao atribuir insumo'),
            true,
            ERROR
         );
      }  
   }

   Html::redirect("{$CFG_GLPI['root_doc']}/plugins/fillglpi/front/limpeza.form.php?id=$newid");
} else if (isset($_POST["update"])) {
   if (isset($_POST['consumables'])) {
      if (Session::haveRight(Consumable::$rightname, UPDATE)) {
         $consumable = new Consumable();

         $consumablesCurrent = [];

         foreach (explode(";", $_POST['consumables']) as $item) {
            $consumablesToAdd[] = intval($item);
         }     

         //pega todos os insumos em uso pela limpeza
         $usedConsumables = BD::getItemConsumable('GlpiPlugin\\\FillGlpi\\\Limpeza', $_POST['id']);      
         
         foreach ($usedConsumables as $id) {
            $consumablesCurrent[] = $id['id'];          
         }   
         
         //remove o ultimo item do post
         array_pop($consumablesToAdd);

         $toRemove = array_diff($consumablesCurrent, $consumablesToAdd);
         $toAdd = array_diff($consumablesToAdd, $consumablesCurrent);

         if (count($toRemove) > 0) {
            foreach ($toRemove as $item) {
               $consumable->backToStock(['id' => $item]);        
            }
         }

         if (count($toAdd) > 0) {
            foreach ($toAdd as $item) {
               $consumable->out($item, 'GlpiPlugin\\FillGlpi\\Limpeza', $_POST['id']);        
            }
         }
      } else {
         Session::addMessageAfterRedirect(
            __('Sem permissão para alterar insumos'),
            true,
            ERROR
         );
      }
   } 
        
   $object->check($_POST['id'], UPDATE, $_POST);
   $object->update($_POST);
   
   Html::back();
} else {
   $withtemplate = (isset($_GET['withtemplate']) ? $_GET['withtemplate'] : "");
   $id = (isset($_GET['id']) ? $_GET['id'] : -1);

   Limpeza::displayFullPageForItem($id, ["helpdesk", Limpeza::class], [
      'withtemplate' => $withtemplate,
      'formoptions'  => "data-track-changes=true",
   ]);
}
