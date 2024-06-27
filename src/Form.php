<?php
namespace GlpiPlugin\Cotrisoja;

use GlpiPlugin\Cotrisoja\Sql;
use CommonDBTM;
use Glpi\Application\View\TemplateRenderer;

class Form extends CommonDBTM {
    public static function inputText($name, $label, $value = '') {
        $output = '<label for="'.$name.'" class="form-label">'.$label.'</label>';
        $output .= '<input type="text" name="'.$name.'" id="'.$name.'" value="'.$value.'"></input>';
        return $output;
    }

    public static function inputDate($name, $label, $value = '') {
        $output = '<label for="'.$name.'" class="form-label">'.$label.'</label>';
        $output .= '<input type="date" name="'.$name.'" id="'.$name.'" value="'.$value.'"></input>';

        return $output;
    }

    public static function inputInt($name, $label, $value = '') {
        $output = '<label for="'.$name.'" class="form-label">'.$label.'</label>';
        $output .= '<input type="number" name="'.$name.'" id="'.$name.'" value="'.$value.'"></input>';

        return $output;
    }

    public static function wrapInDiv($item, $class = []) {
        $classes = implode(' ', $class);
        $output = '
            <div class="'.$classes.'">
                '.$item.'
            </div>  
        ';

        return $output;
    }

    public static function dropdownForTable($name, $label, $field, $value) {
        $table = 'glpi_'.explode('_id', $field)[0];

        $response = Sql::getAllValues($table);

        $output = '<label for="'.$name.'" class="form-label">'.$label.'</label>';
        $output .= '<select name="'.$name.'" id="'.$name.'" value="'.$value.'">';

        foreach ($response as $item) {
            if (array_key_exists('name', $item)) {
                $text = $item['name'];
            } else if (array_key_exists('property_code', $item)) {
                $text = $item['property_code'];
            } else if (array_key_exists('brand', $item)) {
                $text = $item['brand'];
            }

            $output .= '<option value="'.$item['id'].'">'.$text.'</option>';            
        }

        $output .= '</select>';

        return $output;
    }    

    public static function showFormFor($itemType, $ID, $otherFields = [], $hideFields = []) {
        global $DB;        
        $table = $itemType->getTable();
        $findedValue = '';

        $fields = [];

        if ($ID == -1) {
            $mode = 'add';
        } else if ($ID > 0) {
            $mode = 'update';
            $values = Sql::getValuesByID($ID, $table);            
        }           

        $q = $DB->request('DESCRIBE '.$table);

        foreach ($q as $row) {
            if ($mode == 'update') {
                foreach ($values as $value) {
                    if (array_key_exists($row['Field'], $value)) {
                        $findedValue = $value[$row['Field']];
                    }            
                }
            }

            $label = self::getLabelByField($row['Field']);

            if (!in_array($row['Field'], $hideFields)) {
                if (strpos($row['Type'], 'varchar') !== false) {                 
                    $fields[] = [
                        'type' => 'text',
                        'name' => $row['Field'],
                        'label' => $label,
                        'value' => $findedValue
                    ];
                } elseif (strpos($row['Type'], 'date') !== false) {
                    $fields[] = [
                        'type' => 'date',
                        'name' => $row['Field'],
                        'label' => $label,
                        'value' => $findedValue
                    ];   
                } elseif (strpos($row['Type'], 'int') !== false) {
                    if ((strpos($row['Field'], '_id') !== false)) {                   
                        $fieldItemType = getItemtypeForForeignKeyField($row['Field']);

                        $fields[] = [
                            'type' => 'dropdown',
                            'name' => $row['Field'],
                            'label' => $label,
                            'item' => $fieldItemType,
                            'value' => $findedValue
                        ];             
                    } else if ($row['Field'] != 'id') {
                        $fields[] = [
                            'type' => 'number',
                            'name' => $row['Field'],
                            'label' => $label,
                            'value' => $findedValue
                        ]; 
                    } else if ($row['Field'] == 'id' && $row['Extra'] != 'auto_increment') {                    
                        $fields[] = [
                            'type' => 'number',
                            'name' => 'id',
                            'label' => 'ID',
                            'value' => $findedValue
                        ]; 
                    }                
                } 
            }
        }

        $loader = new TemplateRenderer();
        $loader->display('@cotrisoja/default_form.html.twig',
            [
                'id'         => $ID,
                'itemtype'     => $itemType,
                'fields_table' => $fields,
                'other_fields' => $otherFields
            ]
        );
    }

    public static function getLabelByField($field) {
        $label = preg_replace('/plugin_cotrisoja_/', '', $field);
        $label = ucwords(preg_replace('/_/', ' ', $label));
        $label = preg_replace('/Id/', '', $label);
        $label = rtrim($label, '\ \s');
        $label = __($label).': ';   
        
        return $label;
    }

    public static function showSearch($data) {
        $loader = new TemplateRenderer();
        $loader->display('@cotrisoja/showTable_form.html.twig',
            [
                'columns'   =>  $data['header'],
                'values'    =>  $data['values'],
                'view'      =>  'true'
            ]
        );
    }
}