<?php
namespace GlpiPlugin\Cotrisoja;

use GlpiPlugin\Cotrisoja\Form;
use GlpiPlugin\Cotrisoja\NobreakModel;
use CommonDBTM;
use Dropdown;

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
        $otherFields[] = [
            'type'  =>  'quantity',
            'name'  =>  'quantity',
            'label' =>  'Quantidade: '
        ];

        $hideFields = [
            'name'
        ];

        Form::showFormFor($this, $ID, $otherFields, $hideFields);
  
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
            'field'              => 'expire_date',
            'name'               => __('Data de Vencimento'),
            'datatype'           => 'date',
            'massiveaction'      => true
        ];   
        
        $tab[] = [
            'id'                 => '3',
            'table'              => NobreakModel::getTable(),
            'field'              => 'name',
            'joinparams'         => [  
                'beforejoin'  => [
                    'table'      => 'glpi_plugin_cotrisoja_nobreaks',
                    'field'      => 'plugin_cotrisoja_nobreakmodels_id',
                    'jointype'   => 'itemtype_item',
                    'beforejoin' => [
                        'table'      => $this::getTable(),
                        'field'      => 'plugin_cotrisoja_nobreaks_property_code',
                        'jointype'   => 'itemtype_item',
                    ]
                ]            
            ],
            'name'               => __('Modelo Nobreak'),
            'datatype'           => 'itemlink'
        ];
        return $tab;
    }
}