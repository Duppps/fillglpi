<?php

namespace GlpiPlugin\Cotrisoja;

use CommonDropdown;
use Glpi\Application\View\TemplateRenderer;

class Resource extends CommonDropdown {
    public static $rightname = 'plugin_cotrisoja_resources';

    public static function getTypeName($nb = 0) {
        return _n('Resource', 'Resources', $nb);
    }
    
    public static function getIcon() {
        return 'fas fa-mug-saucer';
    } 

    public function rawSearchOptions() {
        $tab[] = [
            'id'                 => '2',
            'table'              => $this::getTable(),
            'field'              => 'name',
            'name'               => __('Name'),
            'datatype'           => 'itemlink',
            'massiveaction'      => true
        ];   
        
        $tab[] = [
            'id'                 => '3',
            'table'              => 'glpi_reservationitems',
            'field'              => 'itemtype',
            'joinparams'         => [  
                'beforejoin'  => [
                    'table'      => $this::getTable(),
                    'field'      => 'plugin_cotrisoja_reservationitems_id',
                    'jointype'   => 'itemtype_item',                    
                ]            
            ],
            'name'               => __('Item'),
            'datatype'           => 'itemlink',
            'massiveaction'      => true
        ]; 

        return $tab;
    }

    public function showForm($ID, array $options = []) {  
        $resource = [];
        $items = [];
        $resourceResults = Sql::getValuesByID($ID, 'glpi_plugin_cotrisoja_resources');

        foreach (Sql::getAllValues('glpi_reservationitems') as $item) {
            $table = getTableForItemType($item['itemtype']);
            $itemName = Sql::getValuesByID($item['items_id'], $table);
            foreach ($itemName as $in) {
                $items[] = [
                    'id'            =>  $item['id'],
                    'itemtype'      =>  $item['itemtype'],
                    'itemTypeName'  =>  $in['name'],
                    'itemTypeId'    =>  $item['items_id']
                ];
            }
            
        }

        foreach ($resourceResults as $result) {
            $resource = [
                'name'                  =>  $result['name'],
                'reservationitems_id'   =>  $result['reservationitems_id']
            ];
        }        

        $loader = new TemplateRenderer();
        $loader->display('@cotrisoja/resource_form.html.twig',
            [
                'id'            =>  $ID,
                'current_value' =>  $resource,
                'items_value'   =>  $items
            ]
        );    
          
        return true;
    }

    public static function create($idResource, $idReservation) {
        Sql::insert('glpi_plugin_cotrisoja_reservations_resources', [
            'plugin_cotrisoja_resources_id'     =>  $idResource,
            'plugin_cotrisoja_reservations_id'  =>  $idReservation
        ]);
    }

    public function getAll() {
        $response = [];

        $results = Sql::getAllValues($this->getTable());
        
        foreach($results as $result) {
            $response[] = [
                'id'    =>  $result['id'],
                'name'  =>  $result['name']
            ];
        }

        return $response;
    }
}