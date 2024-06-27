<?php
namespace GlpiPlugin\FillGlpi;

use GlpiPlugin\FillGlpi\Form;
use CommonDropdown;

class NobreakModel extends CommonDropdown {
    public static $rightname = 'plugin_fillglpi_nobreakmodels';
    public static function getTypeName($nb = 0) {
        return _n('Modelo de Nobreak', 'Modelos de Nobreak', $nb);
    }
    
    public static function getIcon() {
        return 'fas fa-plug-circle-bolt';
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
            'id'                 => '3',
            'table'              => $this::getTable(),
            'field'              => 'brand',
            'name'               => __('Marca'),
            'datatype'           => 'itemlink',
            'massiveaction'      => true
        ];   

        return $tab;
    }
}