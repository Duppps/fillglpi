<?php
namespace GlpiPlugin\Cotrisoja;

use GlpiPlugin\Cotrisoja\Form;
use CommonDBTM;

class Battery extends CommonDBTM {
    public static $rightname = 'plugin_cotrisoja_batteries';

    public static function getTypeName($nb = 0) {
        return _n('Bateria', 'Baterias', $nb);
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
}