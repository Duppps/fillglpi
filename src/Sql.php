<?php

namespace GlpiPlugin\Fillglpi;

class Sql
{
    public static function getConsumableItemTypes()
    {
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

    public static function getConsumableItemsByItemTypes($itemtype)
    {
        global $DB;
        $response = [];

        $object = $DB->request('SELECT * FROM glpi_consumableitems WHERE consumableitemtypes_id = ' . $itemtype . '');

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

    public static function getConsumablesInStockByConsumableItems($consumableItem)
    {
        global $DB;
        $response = [];

        $object = $DB->request('SELECT * FROM glpi_consumables WHERE consumableitems_id = ' . $consumableItem . '');

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

    public static function getUsedConsumablesByItemType($itemType, $ID, $consumableItem)
    {
        global $DB;
        $response = [];

        if (!isset($ID)) {
            $andItemType = '';
        } else {
            $andItemType = ' AND itemtype = "' . $itemType . '"';
        }

        $object = $DB->request('SELECT * FROM glpi_consumables WHERE consumableitems_id = ' . $consumableItem . $andItemType . '  AND items_id = ' . $ID . ' AND date_out IS NOT NULL');

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

    public static function insert($table, $data)
    {
        global $DB;

        $columns = array_keys($data);
        $values = array_values($data);

        $columns_string = implode(',', $columns);
        $rows_string = implode('","', $values);

        $DB->request('INSERT INTO ' . $table . ' (' . $columns_string . ') VALUES ("' . $rows_string . '")');

        return true;
    }

    public static function update($parameters)
    {
        global $DB;

        $DB->request('UPDATE' . $parameters);
    }

    public static function getValuesByID($ID, $table, $field = 'id')
    {
        global $DB;

        return $DB->request(['FROM' => $table, 'WHERE' => [$field => $ID]]);
    }

    public static function getAllValues($table)
    {
        global $DB;

        $response = $DB->request(['FROM' => $table]);

        return $response;
    }

    public static function describe($table)
    {
        global $DB;

        return $DB->request('DESCRIBE ' . $table);
    }

    public static function getSpecificField($field, $table, $qryValue, $qryField)
    {
        global $DB;

        $return = '';

        $rest = $DB->request('SELECT ' . $field . ' FROM ' . $table . ' WHERE ' . $qryField . ' = ' . $qryValue);

        foreach ($rest as $r) {
            $return = $r[$field];
        }

        return $return;
    }

    public static function getOpenReservationInfo($status)
    {
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
                glpi_plugin_fillglpi_reservations gpc ON gr.id = gpc.reservations_id
            INNER JOIN 
                glpi_users gu ON gr.users_id = gu.id
            INNER JOIN 
                glpi_reservationitems gri ON gr.reservationitems_id = gri.id
            WHERE 
                gr.begin " . $opr . " CURRENT_DATE()
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
    public static function getReservationInfo(int $ID)
    {
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
                glpi_plugin_fillglpi_reservations gpc ON gr.id = gpc.reservations_id        
            INNER JOIN 
                glpi_users gu ON gr.users_id = gu.id
            INNER JOIN 
                glpi_reservationitems gri ON gr.reservationitems_id = gri.id            
            WHERE 
                gr.id = " . $ID . "
        ";

        $qryResources = "
                SELECT resources.name
                    FROM
                        glpi_plugin_fillglpi_resources resources
                    INNER JOIN
                        glpi_plugin_fillglpi_resources_reservationsitems resources_reservationsitems
                            ON resources_reservationsitems.plugin_fillglpi_resources_id = resources.id
                    INNER JOIN
                        glpi_plugin_fillglpi_reservations_resources reservations_resources
                            ON reservations_resources.plugin_fillglpi_resources_reservationsitems_id = resources_reservationsitems.id
                    WHERE
                        reservations_resources.plugin_fillglpi_reservations_id = " . $ID;

        $resource = $DB->request($qryResources);
        $result = $DB->request($query);

        foreach ($resource as $c) {
            array_push($resources, [
                'name'              =>  $c['name']
            ]);
        }

        foreach ($result as $r) {
            $itemTable = getTableForItemType($r['itemtype']);
            $itemDetails = self::getValuesByID($r['items_id'], $itemTable);
            $itemName = $itemDetails->current()['name'];

            $response = [
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

    public static function getAllDataWithFieldAndOperator($table, $field, $field_value, $operator)
    {
        global $DB;

        return $DB->request(['FROM' => $table, 'WHERE' => [$field => [$operator, $field_value]]]);
    }

    public static function remove($table, $criteria, $criteriaValue)
    {
        global $DB;

        return $DB->delete($table, [$criteria => $criteriaValue]);
    }

    public static function getResourcesByItemType($id)
    {
        global $DB;
        $a = [];

        $items = $DB->request(
            'SELECT reservation_resources.*, glpi_plugin_fillglpi_resources.name, glpi_plugin_fillglpi_resources.type, glpi_plugin_fillglpi_resources.id as resID FROM glpi_plugin_fillglpi_resources
                INNER JOIN glpi_plugin_fillglpi_resources_reservationsitems reservation_resources
                    ON reservation_resources.plugin_fillglpi_resources_id = glpi_plugin_fillglpi_resources.id
                INNER JOIN glpi_reservationitems reservationitems
                    ON reservationitems.id = reservation_resources.reservationitems_id
                WHERE reservationitems.id = ' . $id . ''
        );

        foreach ($items as $r) {
            $a[] = [
                'id'                => $r['id'],
                'name'              => $r['name'],
                'resID'             => $r['resID'],
                'type'              => $r['type'],
            ];
        }

        return $a;
    }

    public static function getAvailabilityResource($id, $dateStart, $dateEnd)
    {
        global $DB;

        $items = $DB->request(
            'SELECT COUNT(*) as count FROM glpi_reservations
                INNER JOIN glpi_reservationitems resitem
                    ON resitem.id = glpi_reservations.reservationitems_id
                INNER JOIN glpi_plugin_fillglpi_resources_reservationsitems resresvitems
                    ON resresvitems.reservationitems_id = resitem.id
                INNER JOIN glpi_plugin_fillglpi_resources resources
                    ON resources.id = resresvitems.plugin_fillglpi_resources_id
                WHERE
                    (glpi_reservations.begin <= "' . $dateEnd . '") AND (glpi_reservations.end >= "' . $dateStart . '")
                AND
                    resources.id = ' . $id
        );

        $stock = self::getSpecificField('stock', 'glpi_plugin_fillglpi_resources', $id, 'id');

        if ($items->current()['count'] > $stock && $stock !== NULL) {
            return false;
        }

        return true;
    }

    public static function getReservationsResources($resource)
    {
        global $DB;
        $response = [];

        $i = $DB->request(
            'SELECT * FROM glpi_reservations
                INNER JOIN glpi_plugin_fillglpi_reservations_resources grr
                    ON grr.plugin_fillglpi_reservations_id = glpi_reservations.id
                INNER JOIN glpi_plugin_fillglpi_resources_reservationsitems gresres
                    ON gresres.id = grr.plugin_fillglpi_resources_reservationsitems_id
                INNER JOIN glpi_plugin_fillglpi_resources gresources
                    ON gresources.id = gresres.plugin_fillglpi_resources_id
                WHERE gresres.id = ' . intval($resource)
        );

        foreach ($i as $a) {
            $response[] = [
                'id' => $a['id'],
                'begin' => DateFormatter::formatToBr($a['begin']),
                'end'   => DateFormatter::formatToBr($a['end'])
            ];
        }

        return $response;
    }

    public static function purgeData($table, $valueToSearch, $fieldToSearch)
    {
        global $DB;

        $DB->delete($table, [$fieldToSearch => $valueToSearch]);
    }

    public static function getReservationItemName($reservationItemId) {
        global $DB;

        $qryItemType = $DB->request('SELECT itemtype, items_id FROM glpi_reservationitems WHERE id = '.$reservationItemId);
        
        $itemTable = getTableForItemType($qryItemType->current()['itemtype']);
        $itemId = $qryItemType->current()['items_id'];


        return $DB->request('
            SELECT name FROM '.$itemTable.' WHERE id = '.$itemId
        )->current()['name'];

    }
}
