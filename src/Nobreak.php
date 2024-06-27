<?php
namespace GlpiPlugin\Cotrisoja;

use GlpiPlugin\Cotrisoja\Form;
use GlpiPlugin\Cotrisoja\NobreakModel;
use CommonDBTM;
use Location;

class Nobreak extends CommonDBTM {
    public static $rightname = 'plugin_cotrisoja_nobreaks';

    public static function getTypeName($nb = 0) {
        return _n('Nobreak', 'Nobreaks', $nb);
    }
    
    public static function getIcon() {
        return 'fas fa-plug-circle-bolt';
    }      

    public function showForm($ID, array $options = []) {
        $hideFields = [
            'name'
        ];             

        Form::showFormFor($this, $ID, [], $hideFields);
  
        return true;
    }

    public function rawSearchOptions() {       
        $tab[] = [
            'id'                 => '2',
            'table'              => $this::getTable(),
            'field'              => 'asset_number',
            'name'               => __('Patrimônio'),
            'datatype'           => 'itemlink',
            'massiveaction'      => true
        ]; 

        $tab[] = [
            'id'                 => '3',
            'table'              => NobreakModel::getTable(),
            'field'              => 'name',
            'joinparams'         => [  
                'beforejoin'  => [
                    'table'      => $this::getTable(),
                    'field'      => 'plugin_cotrisoja_nobreakmodels_id',
                    'jointype'   => 'itemtype_item',                    
                ]            
            ],
            'name'               => __('Modelo Nobreak'),
            'datatype'           => 'itemlink',
            'massiveaction'      => true
        ];

        $tab[] = [
            'id'                 => '4',
            'table'              => Location::getTable(),
            'field'              => 'name',
            'joinparams'         => [  
                'beforejoin'  => [
                    'table'      => $this::getTable(),
                    'field'      => 'locations_id',
                    'jointype'   => 'itemtype_item',                    
                ]            
            ],
            'name'               => __('Localização'),
            'datatype'           => 'itemlink',
            'massiveaction'      => true
        ];

        return $tab;
    }
}