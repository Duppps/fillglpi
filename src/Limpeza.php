<?php
namespace GlpiPlugin\Cotrisoja;

use CommonDBTM;
use CommonGLPI;
use Computer;
use Consumable;
use Session;
use Glpi\Application\View\TemplateRenderer;

class Limpeza extends CommonDBTM {
   public static $rightname = 'plugin_cotrisoja_limpezas';

   public static function getTypeName($nb = 0) {
      return _n('Limpeza', 'Limpezas', $nb);
   }
  
   public static function getIcon() {
      return 'fas fa-wrench';
   }    

   public function showForm($ID, array $options = []) {     
      $this->initForm($ID, $options);                    
      TemplateRenderer::getInstance()->display(
         '@cotrisoja/limpeza.html.twig',
         [
            'item'       => $this            
         ]
      );

      return true;
   }

   public function defineTabs($options = []) {
      $ong = [];
      $this->addDefaultFormTab($ong)
         ->addImpactTab($ong, $options);
         
      return $ong;
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
      switch ($item::getType()) {
         case Computer::getType():
            return $this::getTypeName();
      }
      return '';
   }
   
   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      switch ($item::getType()) {
         case Computer::getType():
            echo "as";     
            
            break;    
      }      
      return true;
   }  

   public static function itemPurge(CommonDBTM $item) {
      global $DB;
      $idItem = $item->getID();
      $obj = new Consumable();

      $consumables = $DB->request([
         'FROM'   => 'glpi_consumables',
         'WHERE'  => [
            'itemtype'  => 'GlpiPlugin\\Cotrisoja\\Limpeza',
            'items_id'  => $idItem
         ]
      ]);

      foreach ($consumables as $consumable) {
         $obj->backToStock(['id' => $consumable['id']]);
      }
   }
}
