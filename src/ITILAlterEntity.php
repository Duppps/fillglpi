<?php
namespace GlpiPlugin\Cotrisoja;

use CommonDBChild;
use Session;

class ITILAlterEntity extends CommonDBChild {

    public static function getIcon() {
        return 'ti ti-stack';
    }

    public static function getTypeName($nb = 0) {
        return _n('Alterar Entidade', 'Alterar Entidade', $nb);
    }    

    public function canAddSolution() {
        if (Session::haveRight('ticket', UPDATE)) {
            return true;
        } 
        return false;
    }
}