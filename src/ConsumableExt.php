<?php

namespace GlpiPlugin\Fillglpi;

use CommonDBTM;
use GlpiPlugin\Fillglpi\Sql;

class ConsumableExt extends CommonDBTM {
    public static function getUsedConsumablesByItemType($itemtype, $itemID) {
        $itemtype = str_replace('\\', '\\\\', $itemtype);
        $consumableTypes = Sql::getConsumableItemTypes();       
    
        foreach ($consumableTypes as &$type) {        
            $type['consumables'] = Sql::getConsumableItemsByItemTypes($type['id']);  
            
            foreach ($type['consumables'] as &$consumable) {
                $consumable['amount'] = Sql::getUsedConsumablesByItemType($itemtype, $itemID, $consumable['id']);                
            }          

            $type['consumables'] = array_filter($type['consumables'], function($consumable) {
                return count($consumable['amount']) > 0;
            });

            $type['consumables'] = array_values($type['consumables']);
        }  
        
        $consumableTypes = array_filter($consumableTypes, function($consumableType) {
            return count($consumableType['consumables']) > 0;
        });

        $consumableTypes = array_values($consumableTypes);

        return json_encode($consumableTypes);  
    }

    public static function getConsumablesAndItemTypes($exception = '') {
        $consumableTypes = Sql::getConsumableItemTypes();

        if ($consumableTypes) {
            $lastType = end($consumableTypes);
            $newId = $lastType['id'] + 1;
        } else {
            $newId = 1;
        }
        
        $others = [
            'id' => $newId,
            'name' => 'Outros'
        ];
        
        $consumableTypes[] = $others;
        

        foreach ($consumableTypes as &$type) {
            if ($type['name'] != 'Outros') {           
                $type['consumables'] = Sql::getConsumableItemsByItemTypes($type['id']);                 
            } else {                
                $type['consumables'] = Sql::getConsumableItemsByItemTypes(0);
            } 

            foreach ($type['consumables'] as &$consumable) {
                $consumable['amount'] = Sql::getConsumablesInStockByConsumableItems($consumable['id']);

                $consumable['amount'] = array_filter($consumable['amount'], function($item) use ($exception) {                    
                    return ($item['date_out'] == null) || $item['itemtype'] == $exception;
                });

                $consumable['amount'] = array_values($consumable['amount']);
            } 
            
            $type['consumables'] = array_filter($type['consumables'], function($consumable) {
                return count($consumable['amount']) > 0;
            });

            $type['consumables'] = array_values($type['consumables']);
        }

        $consumableTypes = array_filter($consumableTypes, function($consumableType) {
            return count($consumableType['consumables']) > 0;
        });

        $consumableTypes = array_values($consumableTypes);

        return json_encode($consumableTypes);  
    }    
}