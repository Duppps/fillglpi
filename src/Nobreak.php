<?php
namespace GlpiPlugin\Cotrisoja;

use GlpiPlugin\Cotrisoja\Form;
use CommonDBTM;

class Nobreak extends CommonDBTM {
    public static $rightname = 'plugin_cotrisoja_nobreaks';

    public static function getTypeName($nb = 0) {
        return _n('Nobreak', 'Nobreaks', $nb);
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
            'field'              => 'id',
            'name'               => __('ID'),
            'datatype'           => 'itemlink',
            'massiveaction'      => false
        ];

        $tab[] = [
            'id'                 => '2',
            'table'              => $this::getTable(),
            'field'              => 'property_code',
            'name'               => __('Patrimônio'),
            'datatype'           => 'itemlink',
            'massiveaction'      => false
        ]; 

        return $tab;
    }
}