<?php
namespace GlpiPlugin\Cotrisoja;

use GlpiPlugin\Cotrisoja\Sql;
use CommonDBTM;
use Session;
use Dropdown;

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

    public static function showFormFor($itemType, $ID) {
        global $DB;
        $table = $itemType->getTable();
        $findedValue = '';

        if ($ID == -1) {
            $mode = 'add';
        } else if ($ID > 0) {
            $mode = 'update';
            $values = Sql::getValuesByID($ID, $table);            
        }      

        $output = '<form name="asset_form" style="width: 100%;" class="d-flex flex-column" method="post" action="'.$itemType->getFormURL() .'" enctype="multipart/form-data" data-track-changes="true" data-submit-once>';
        $output .= '<input type="hidden" name="itemtype" value="'.getItemTypeForTable($table).'" />';
        $output .= '<input type="hidden" name="items_id" value="'.$ID.'" />';
        $output .= '<input type="hidden" name="_no_message_link" value="1" />';

        $q = $DB->request('DESCRIBE '.$table);

        foreach ($q as $row) {
            if ($mode == 'update') {
                foreach ($values as $value) {
                    if (array_key_exists($row['Field'], $value)) {
                        $findedValue = $value[$row['Field']];
                    }            
                }
            }

            $label = preg_replace('/plugin_cotrisoja_/', '', $row['Field']);
            $label = ucwords(preg_replace('/_/', ' ', $label));
            $label = preg_replace('/Id/', '', $label);

            if (strpos($row['Type'], 'varchar') !== false) { 
                $output .= self::wrapInDiv(self::inputText($row['Field'], $label, $findedValue));               
            } elseif (strpos($row['Type'], 'date') !== false) {
                $output .= self::wrapInDiv(self::inputDate($row['Field'], $label, $findedValue), ["mt-2"]);     
            } elseif (strpos($row['Type'], 'int') !== false) {
                if (strpos($row['Field'], '_id') !== false) {               
                    $output .= self::wrapInDiv(self::dropdownForTable($row['Field'], $label, $row['Field'], $findedValue), ['mt-2']);      
                } else if ($row['Field'] != 'id') {
                    $output .= self::wrapInDiv(self::inputInt($row['Field'], $label, $findedValue), ["mt-2"]);
                }
            } 
        }

        $output .= '<input type="hidden" name="_glpi_csrf_token" value="'.Session::getNewCSRFToken().'" />';
        $output .= '<input type="hidden" name="id" value="'.$ID.'" />';
        $output .= self::wrapInDiv('<button type="submit" name="'.$mode.'" class="btn btn-primary">Enviar</button>', ['mt-2']); 
        $output .= '</form>';

        echo $output;
    }
}