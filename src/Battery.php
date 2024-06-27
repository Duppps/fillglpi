<?php
namespace GlpiPlugin\FillGlpi;

use GlpiPlugin\FillGlpi\Form;
use GlpiPlugin\FillGlpi\Nobreak;
use GlpiPlugin\FillGlpi\NobreakModel;
use GlpiPlugin\FillGlpi\BatteryModel;
use CommonDBTM;
use CommonGLPI;

class Battery extends CommonDBTM {
    public static $rightname = 'plugin_fillglpi_batteries';

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
        Sql::update('glpi_plugin_fillglpi SET plugin_fillglpi_nobreaks_id NULL '.$where);
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
                    'field'      => 'plugin_fillglpi_batterymodels_id',
                    'jointype'   => 'itemtype_item',                    
                ]            
            ],
            'name'               => __('Modelo'),
            'datatype'           => 'itemlink',
            'massiveaction'      => true
        ]; 

        $tab[] = [
            'id'                 => '3',
            'table'              => BatteryModel::getTable(),
            'field'              => 'brand',
            'joinparams'         => [  
                'beforejoin'  => [
                    'table'      => $this::getTable(),
                    'field'      => 'plugin_fillglpi_batterymodels_id',
                    'jointype'   => 'itemtype_item',                    
                ]            
            ],
            'name'               => __('Marca'),
            'datatype'           => 'itemlink',
            'massiveaction'      => true
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
                    'table'         => $this::getTable(),
                    'field'         => 'plugin_fillglpi_nobreaks_id',
                    'jointype'      => 'itemtype_item', 
                    'massiveaction' => true                   
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
                    'table'      => 'glpi_plugin_fillglpi_nobreaks',
                    'field'      => 'plugin_fillglpi_nobreakmodels_id',
                    'jointype'   => 'itemtype_item',
                    'beforejoin' => [
                        'table'      => $this::getTable(),
                        'field'      => 'plugin_fillglpi_nobreaks_property_code',
                        'jointype'   => 'itemtype_item',
                    ]
                ]            
            ],
            'name'               => __('Modelo Nobreak'),
            'datatype'           => 'itemlink',
            'massiveaction'      => false
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
                    'name'      =>  'plugin_fillglpi_nobreaks_id',
                    'label'     =>  '',
                    'value'     =>  $item->getID(),
                    'display'   =>  'none'
                ];   

                $p = [
                    'start'      => 0,
                    'is_deleted' => 0, 
                    'criteria'   => [[
                            'field'      => 5,        
                            'searchtype' => 'equals',  
                            'value'      => $item->getID(),         
                        ],
                    ]];

                echo    '<ul class="nav nav-tabs" id="tabItem" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="create-tab" data-bs-toggle="tab" data-bs-target="#create-tab-pane" type="button" role="tab">';
                echo                __('Add');
                echo            '</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#list-tab-pane" type="button" role="tab">';
                echo                __('List');
                echo            '</button>
                            </li>  
                        </ul>
                        <div class="tab-content" id="tbContent">
                            <div class="tab-pane fade show active mb-2" id="create-tab-pane" role="tabpanel" aria-labelledby="create-tab" tabindex="0">';
                                Form::showFormFor($obj->getClass(), -1, $otherFields, ['plugin_fillglpi_nobreaks_id', 'name']);               
                echo        '</div>
                            <div class="tab-pane fade mb-2" id="list-tab-pane" role="tabpanel" tabindex="0">';
                echo            \Search::showList(self::class, $p);
                echo        '</div>
                        </div>';                       

                break;
        }
        return true;
    }
}