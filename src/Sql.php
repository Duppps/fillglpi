<?php

namespace GlpiPlugin\Cotrisoja;

class Sql {
    public static function getConsumableItemTypes() {
        global $DB;
        $response = [];

        $object = $DB->request('SELECT * FROM glpi_consumableitemtypes');

        foreach ($object as $item) {
            $response[] = [
                'id' => $item['id'],
                'name' => $item['name']
            ];
        }

        return $response;
    }
    
    public static function getConsumableItemsByItemTypes($itemtype) {
        global $DB;
        $response = [];

        $object = $DB->request('SELECT * FROM glpi_consumableitems WHERE consumableitemtypes_id = '.$itemtype.'');

        foreach ($object as $item) {
            $response[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'consumableitemtypes_id' => $item['consumableitemtypes_id'],
                'entities_id' => $item['entities_id']
            ];
        }

        return $response;
    }

    public static function getConsumablesInStockByConsumableItems($consumableItem) {
        global $DB;
        $response = [];

        $object = $DB->request('SELECT * FROM glpi_consumables WHERE consumableitems_id = '.$consumableItem.'');

        foreach ($object as $item) {
            $response[] = [
                'id' => $item['id'],
                'consumableitems_id' => $item['consumableitems_id'],
                'date_in' => $item['date_in'],
                'date_out' => $item['date_out'],
                'itemtype' => $item['itemtype'],
                'items_id' => $item['items_id']
            ];
        }

        return $response;
    }

    public static function getUsedConsumablesByItemType($itemType, $ID, $consumableItem) {
        global $DB;
        $response = [];

        if (!isset($ID)) {
            $andItemType = '';
        } else {
            $andItemType = ' AND itemtype = "'.$itemType.'"';
        }

        $object = $DB->request('SELECT * FROM glpi_consumables WHERE consumableitems_id = '.$consumableItem.$andItemType.'  AND items_id = '.$ID.' AND date_out IS NOT NULL');

        foreach ($object as $item) {
            $response[] = [
                'id' => $item['id'],
                'consumableitems_id' => $item['consumableitems_id'],
                'date_in' => $item['date_in'],
                'date_out' => $item['date_out'],
                'itemtype' => $item['itemtype'],
                'items_id' => $item['items_id']
            ];
        }

        return $response;
    }  
    
    public static function insert($table, $data) {
        global $DB;                  

        $columns = array_keys($data);
        $values = array_values($data);

        $columns_string = implode(',', $columns);
        $rows_string = implode('","', $values);
    
        $DB->request('INSERT INTO ' . $table . ' (' . $columns_string . ') VALUES ("' . $rows_string . '")');
    
        return true;
    }

    public static function update($parameters) {
        global $DB;

        $DB->request('UPDATE'.$parameters);
    }

    public static function getValuesByID($ID, $table, $field = 'id') {
        global $DB;

        return $DB->request(['FROM' => $table, 'WHERE' => [$field => $ID]]);        
    }

    public static function getAllValues($table) {
        global $DB;

        $response = $DB->request(['FROM' => $table]);                 
        
        return $response;        
    }

    public static function describe($table) {
        global $DB;

        return $DB->request('DESCRIBE '.$table);
    }

    public static function getSpecificField($field, $table, $qryValue, $qryField) {
        global $DB;

        $rest = $DB->request('SELECT '.$field.' FROM '.$table.' WHERE '.$qryField.' = '.$qryValue);

        foreach ($rest as $r) {
            $return = $r[$field];
        }

        return $return;
    }

    public static function getOpenReservationInfo($status) {
        global $DB;
        $response = [];

        if ($status == 'open') {
            $opr = '>';
        } else if ($status == 'closed') {
            $opr = '<';
        }

        $query = "
            SELECT 
                gr.id AS reservation_id,
                gr.begin,
                gr.end,
                gu.name AS user_name,
                gri.itemtype,
                gri.items_id
            FROM 
                glpi_reservations gr
            INNER JOIN 
                glpi_plugin_cotrisoja_reservations gpc ON gr.id = gpc.reservations_id
            INNER JOIN 
                glpi_users gu ON gr.users_id = gu.id
            INNER JOIN 
                glpi_reservationitems gri ON gr.reservationitems_id = gri.id
            WHERE 
                gr.begin ".$opr." CURRENT_DATE()
            ORDER BY gr.begin           
        ";

        $result = $DB->request($query);

        foreach ($result as $r) {
            $itemTable = getTableForItemType($r['itemtype']);
            $itemDetails = self::getValuesByID($r['items_id'], $itemTable);
            $itemName = $itemDetails->current()['name'];

            $response[] = [
                'id'        =>  $r['reservation_id'],
                'item'      =>  $itemName,
                'user'      =>  $r['user_name'],
                'begin'     =>  DateFormatter::formatToBr($r['begin']),
                'end'       =>  DateFormatter::formatToBr($r['end'])
            ];            
        }      
        
        return $response;
    }

    /**
    * Return core informations about the reservation
    *
    * @param int $ID Reservation (glpi_reservation) ID

    * @return array data from reservation
    */ 
    public static function getReservationInfo(int $ID) {
        global $DB;
        $response = [];
        $resources = [];

        $query = "
            SELECT 
                gr.id AS reservation_id,
                gr.begin,
                gr.end,
                gr.comment,
                gu.name AS user_name,
                gri.itemtype,
                gri.items_id,
                gri.id AS reservationItemID,
                gpc.people_quantity
            FROM 
                glpi_reservations gr
            INNER JOIN 
                glpi_plugin_cotrisoja_reservations gpc ON gr.id = gpc.reservations_id         
            INNER JOIN 
                glpi_users gu ON gr.users_id = gu.id
            INNER JOIN 
                glpi_reservationitems gri ON gr.reservationitems_id = gri.id
            WHERE 
                gr.id = ".$ID."
        ";

        $result = $DB->request($query);

        foreach ($result as $r) {
            $itemTable = getTableForItemType($r['itemtype']);
            $itemDetails = self::getValuesByID($r['items_id'], $itemTable);
            $itemName = $itemDetails->current()['name'];

            foreach (self::getValuesByID($r['reservation_id'], 'glpi_plugin_cotrisoja_reservations_resources', 'plugin_cotrisoja_reservations_id') as $b) {
                foreach (self::getValuesByID($b['plugin_cotrisoja_resources_id'], 'glpi_plugin_cotrisoja_resources') as $c) {
                    array_push($resources, $c['name']);
                }    
            }                   

            $response[] = [
                'user'              =>  $r['user_name'],
                'itemName'          =>  $itemName,
                'begin'             =>  DateFormatter::formatToBr($r['begin']),
                'end'               =>  DateFormatter::formatToBr($r['end']),
                'comment'           =>  $r['comment'],
                'peopleQuantity'    =>  $r['people_quantity'],
                'recursos'          =>  $resources
            ];                    
        }    
        
        return $response;
    }
}