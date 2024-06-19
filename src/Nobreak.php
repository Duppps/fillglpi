<?php
namespace GlpiPlugin\Cotrisoja;

use GlpiPlugin\Cotrisoja\Form;
use GlpiPlugin\Cotrisoja\NobreakModel;
use CommonDBTM;

class Nobreak extends CommonDBTM {
    public static $rightname = 'plugin_cotrisoja_nobreaks';

    public static function getTypeName($nb = 0) {
        return _n('Nobreak', 'Nobreaks', $nb);
    }
    
    public static function getIcon() {
        return 'fas fa-plug-circle-bolt';
    }  

    public static function canView() {
        return true;
    }

    public static function canCreate() {
        return true;
    }

    public function showForm($ID, array $options = []) {
        $hideFields = [
            'name'
        ];             

        Form::showFormFor($this, $ID, [], $hideFields);
  
        return true;
    }

    public function searchOptionsNew() {       
        $tab[] = [
            'id'                 => '2',
            'table'              => $this::getTable(),
            'field'              => 'id',
            'name'               => __('Patrimônio'),
            'datatype'           => 'itemlink',
            'massiveaction'      => false
        ]; 

        return $tab;
    }
}