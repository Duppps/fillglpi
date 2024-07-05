<?php

namespace GlpiPlugin\Fillglpi;

use CommonDropdown;
use Session;
use Html;
use Glpi\Application\View\TemplateRenderer;

class Resource extends CommonDropdown {
    public static $rightname = 'plugin_fillglpi_resources';

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
            'table'              => $this::getTable(),
            'field'              => 'stock',
            'name'               => __('Stock'),
            'datatype'           => 'itemlink',
            'massiveaction'      => true
        ];

        return $tab;
    }

    public function showForm($ID, array $options = []) {  
        global $DB;
        $resource = [];
        $items = [];
        $actualItems = [];

        $resourceResults = Sql::getValuesByID($ID, 'glpi_plugin_fillglpi_resources');
        $resourcesReservationItems = $DB->request(
            'SELECT *
                FROM glpi_reservationitems
                INNER JOIN glpi_plugin_fillglpi_resources_reservationsitems
                    ON glpi_plugin_fillglpi_resources_reservationsitems.reservationitems_id = glpi_reservationitems.id
                WHERE glpi_plugin_fillglpi_resources_reservationsitems.plugin_fillglpi_resources_id = '.$ID
            );

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

        foreach ($resourcesReservationItems as $i) {
            $actualItems[] = [
                'id'    =>  $i['reservationitems_id'],
                'name'  =>  Sql::getSpecificField('name', getTableForItemType($i['itemtype']), $i['items_id'], 'id')
            ];
        }

        foreach ($resourceResults as $result) {
            $resource = [
                'name'                         =>  $result['name'],
                'stock'                        =>  $result['stock'],
                'ticket_entities_id'           =>  $result['ticket_entities_id']
            ];
        }        

        $loader = new TemplateRenderer();
        $loader->display('@fillglpi/resource_form.html.twig',
            [
                'id'            =>  $ID,
                'current_value' =>  $resource,
                'items_value'   =>  $items,
                'current_items' =>  $actualItems
            ]
        );    
          
        return true;
    }
   

    public static function create($idResource, $idReservation) {
        $reservationData = Sql::getValuesByID($idReservation, 'glpi_reservations')->current();
        $resourceData = Sql::getValuesByID($idResource, 'glpi_plugin_fillglpi_resources_reservationsitems')->current();

        $iditem = $reservationData['reservationitems_id'];
        $iditemRes = $resourceData['reservationitems_id'];

        if ($iditem == $iditemRes) {
            if (Sql::getAvailabilityResource($resourceData['plugin_fillglpi_resources_id'], $reservationData['begin'], $reservationData['end'])) {
                Sql::insert('glpi_plugin_fillglpi_reservations_resources', [
                    'plugin_fillglpi_resources_reservationsitems_id'        =>  $idResource,
                    'plugin_fillglpi_reservations_id'                       =>  $idReservation
                ]);               

                $resourceTarget = Sql::getValuesByID($resourceData['plugin_fillglpi_resources_id'], 'glpi_plugin_fillglpi_resources')->current();

                if ($resourceTarget['ticket_entities_id']) {
                    $ticket = [
                        'entities_id'       =>  $resourceTarget['ticket_entities_id'],
                        'name'              =>  'Reserva para '.$resourceTarget['name'],
                        'content'           =>  'Reserva para o '.$resourceTarget['name'].' na data '.$reservationData['begin'],
                        'date'              =>  date('Y-m-d h:i:s', time()),
                        'requesttypes_id'   =>  1,
                        'status'            =>  1
                    ];

                    $track = new \Ticket();
                    //$track->check(-1, CREATE, $ticket);
                    $track->add($ticket);
                }

                return true;
            } else {
                Session::addMessageAfterRedirect(
                    __('Recurso não disponível'),
                    false,
                    ERROR
                );
            }   
        }          
         
        return false;      
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

    public static function removeFromResourcesReservationItems($id) {
        Sql::remove('glpi_plugin_fillglpi_resources_reservationsitems', 'plugin_fillglpi_resources_id', $id);
    }

    public static function getResourceByItemTypeAndCheckAvailability($idItemType, $dateStart, $dateEnd) {
        $data = [];

        foreach (Sql::getResourcesByItemType($idItemType) as $i) {      

            $data[] = [
                'id'            =>  $i['id'],
                'name'          =>  $i['name'],
                'availability'  =>  Sql::getAvailabilityResource($i['resID'], $dateStart, $dateEnd)
            ];
        }        

        return $data;
    }
}