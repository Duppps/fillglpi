<?php
namespace GlpiPlugin\Cotrisoja;

use GlpiPlugin\Cotrisoja\Form;
use GlpiPlugin\Cotrisoja\Nobreak;
use GlpiPlugin\Cotrisoja\NobreakModel;
use GlpiPlugin\Cotrisoja\BatteryModel;
use CommonDBTM;
use CommonGLPI;

class Battery extends CommonDBTM {
    public static $rightname = 'plugin_cotrisoja_batteries';

    public static function getTypeName($nb = 0) {
        return _n('Bateria', 'Baterias', $nb);
    }
    
    public static function getIcon() {
        return 'fas fa-car-battery';
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

    public function getClass() {
        return $this;
    }

    public static function out($where) {
        Sql::update('glpi_plugin_cotrisoja SET plugin_cotrisoja_nobreaks_id NULL '.$where);
    }

    public function rawSearchOptions() {
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
            'table'              => BatteryModel::getTable(),
            'field'              => 'name',
            'joinparams'         => [  
                'beforejoin'  => [
                    'table'      => $this::getTable(),
                    'field'      => 'plugin_cotrisoja_batterymodels_id',
                    'jointype'   => 'itemtype_item',                    
                ]            
            ],
            'name'               => __('Modelo'),
            'datatype'           => 'itemlink'
        ]; 

        $tab[] = [
            'id'                 => '3',
            'table'              => BatteryModel::getTable(),
            'field'              => 'brand',
            'joinparams'         => [  
                'beforejoin'  => [
                    'table'      => $this::getTable(),
                    'field'      => 'plugin_cotrisoja_batterymodels_id',
                    'jointype'   => 'itemtype_item',                    
                ]            
            ],
            'name'               => __('Marca'),
            'datatype'           => 'itemlink'
        ]; 
        
        $tab[] = [
            'id'                 => '4',
            'table'              => $this::getTable(),
            'field'              => 'expire_date',
            'name'               => __('Data de Vencimento'),
            'datatype'           => 'date',
            'massiveaction'      => true
        ]; 
        
        $tab[] = [
            'id'                 => '5',
            'table'              => Nobreak::getTable(),
            'field'              => 'id',
            'joinparams'         => [  
                'beforejoin'  => [
                    'table'      => $this::getTable(),
                    'field'      => 'plugin_cotrisoja_nobreaks_id',
                    'jointype'   => 'itemtype_item',                    
                ]            
            ],
            'name'               => __('Patrimônio Nobreak'),
            'datatype'           => 'itemlink'
        ];
        
        $tab[] = [
            'id'                 => '6',
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

    function defineTabs($options=array()) {
        $ong = array();
        $this->addDefaultFormTab($ong);

        return $ong;
    }          
     
    function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
        switch ($item::getType()) {
            case Nobreak::getType():
                return _n('Battery', 'Batteries', 2);
        }
        return '';
    }
     
    static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {       
        switch ($item::getType()) {
            case Nobreak::getType():
                $obj = new self();

                $otherFields[] = [
                    'type'  =>  'quantity',
                    'name'  =>  'quantity',
                    'label' =>  'Quantidade: '
                ];

                $otherFields[] = [
                    'type'      =>  'text',
                    'name'      =>  'plugin_cotrisoja_nobreaks_id',
                    'label'     =>  '',
                    'value'     =>  $item->getID(),
                    'display'   =>  'none'
                ];
                
                Form::showFormFor($obj->getClass(), -1, $otherFields, ['plugin_cotrisoja_nobreaks_id', 'name']);

                break;
        }
        return true;
    }
}