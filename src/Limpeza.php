<?php
namespace GlpiPlugin\Cotrisoja;

use CommonDBTM;
use CommonGLPI;
use Computer;
use Consumable;
use Glpi\Application\View\TemplateRenderer;
use Reservation;

class Limpeza extends CommonDBTM {
   public static $rightname = 'plugin_cotrisoja_limpezas';

   public static function getTypeName($nb = 0) {
      return _n('Limpeza', 'Limpezas', $nb);
   }
  
   public static function getIcon() {
      return 'fas fa-broom';
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

   public function rawSearchOptions() {
      $tab[] = [
         'id'                 => '1',
         'table'              => $this::getTable(),
         'field'              => 'id',
         'name'               => __('ID'),
         'datatype'           => 'itemlink',
         'massiveaction'      => false
      ];
 
      $tab[] = [
         'id'                 => '2',
         'table'              => 'glpi_computers',
         'field'              => 'name',
         'name'               => __('Computador'),
         'datatype'           => 'itemlink'
      ];
 
      $tab[] = [
         'id'                 => '3',
         'table'              => 'glpi_users',
         'field'              => 'name',
         'joinparams'         => [  
            'beforejoin'  => [
               'table'      => 'glpi_computers',
               'field'      => 'users_id',
               'jointype'   => 'itemtype_item',
               'beforejoin' => [
                  'table'      => $this::getTable(),
                  'field'      => 'computers_id',
                  'jointype'   => 'itemtype_item',
               ]
            ]            
         ],
         'name'               => __('User'),
         'datatype'           => 'itemlink'
      ];    
 
      $tab[] = [
         'id'                 => '4',
         'table'              => $this::getTable(),
         'field'              => 'date',
         'name'               => __('Date'),
         'datatype'           => 'date'
      ];
 
     $tab[] = [
         'id'                 => '5',
         'table'              => $this::getTable(),
         'field'              => 'observation',
         'name'               => __('Note'),
         'datatype'           => 'varchar'
     ];

      return $tab;
   }
}
