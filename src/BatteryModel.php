<?php
namespace GlpiPlugin\Fillglpi;

use GlpiPlugin\Fillglpi\Form;
use CommonDropdown;

class BatteryModel extends CommonDropdown {
    public static $rightname = 'plugin_fillglpi_batterymodels';
    public static function getTypeName($nb = 0) {
        return _n('Modelo de Bateria', 'Modelos de Bateria', $nb);
    }
    
    public static function getIcon() {
        return 'fas fa-car-battery';
    }     

    public function showForm($ID, array $options = []) {                    
        Form::showFormFor($this, $ID);
  
        return true;
    }

    public function rawSearchOptions() {
        $tab[] = [
            'id'                 => '1',
            'table'              => $this::getTable(),
            'field'              => 'name',
            'name'               => __('Name'),
            'datatype'           => 'itemlink',
            'massiveaction'      => true
        ];   
        
        $tab[] = [
            'id'                 => '2',
            'table'              => $this::getTable(),
            'field'              => 'brand',
            'name'               => __('Marca'),
            'datatype'           => 'itemlink',
            'massiveaction'      => true
        ];   

        return $tab;
    }
}