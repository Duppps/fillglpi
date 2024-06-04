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
}