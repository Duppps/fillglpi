<?php
namespace GlpiPlugin\Cotrisoja;

use CommonDBTM;

class NobreakModel extends CommonDBTM {
    public static function getTypeName($nb = 0) {
        return _n('Modelo de Nobreak', 'Modelos de Nobreak', $nb);
    }
    
    public static function getIcon() {
        return 'fas fa-car-battery';
    }
    
    public static function canView() {
        return true;
    }

    public static function canCreate() {
        return true;
    }

    public function showForm($ID, array $options = []) {                    
        Form::showFormFor($this, $ID);
  
        return true;
    }

    public function searchOptionsNew() {
        $tab[] = [
            'id'                 => '1',
            'table'              => $this::getTable(),
            'field'              => 'name',
            'name'               => __('Name'),
            'datatype'           => 'itemlink',
            'massiveaction'      => false
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