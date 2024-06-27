<?php

use GlpiPlugin\Fillglpi\BD;
use Consumable;

$AJAX_INCLUDE = 1;

include('../../../inc/includes.php');

header("Content-Type: application/json; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

if (isset($_GET["tiposInsumos"]) && $_GET["tiposInsumos"] == "pass") {
    $consumableTypes = BD::buscaTiposInsumos();

    if ($consumableTypes) {
        $lastType = end($consumableTypes);
        $newId = $lastType['id'] + 1;
    } else {
        $newId = 1;
    }
    
    $others = [
        'id' => $newId,
        'nome' => 'Outros'
    ];
    
    $consumableTypes[] = $others;
    

    foreach ($consumableTypes as &$type) {
        if ($type['nome'] != 'Outros') {           
            $type['consumables'] = BD::buscaInsumosPorTipo(' WHERE consumableitemtypes_id = '.$type['id']);
        } else {
            $type['consumables'] = BD::buscaInsumosPorTipo(' WHERE consumableitemtypes_id = 0');
        } 
        
        $type['consumables'] = array_filter($type['consumables'], function($consumable) {
            return $consumable['quantidade'] > 0;
        });

        $type['consumables'] = array_values($type['consumables']);
    }

    $consumableTypes = array_filter($consumableTypes, function($consumableType) {
        return count($consumableType['consumables']) > 0;
    });

    $consumableTypes = array_values($consumableTypes);

    $result = json_encode($consumableTypes);    
}

if (isset($_GET['insumosPorTipo'])) {

    if($_GET['insumosPorTipo'] != 'others') {
        $consumables = BD::buscaInsumosPorTipo(' WHERE consumableitemtypes_id = '.$_GET['insumosPorTipo']);
    } else {
        $consumables = BD::buscaInsumosPorTipo(' WHERE consumableitemtypes_id = 0');
    }
    
    $result = json_encode($consumables);
}

if (isset($_GET['IDInsumosQNTD'])) {
    $result = Consumable::getUnusedNumber($_GET['IDInsumosQNTD']);
}

echo $result;

