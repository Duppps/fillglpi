<?php
namespace GlpiPlugin\Cotrisoja;

use CommonDBChild;
use TemplateRenderer;
use Consumable;
use CommonITILObject;
use Session;

class ITILInsumo extends CommonDBChild {

    public static function getIcon() {
        return 'ti ti-package';
    }

    public static function getTypeName($nb = 0) {
        return _n('Insumo por Chamado', 'Insumos por Chamados', $nb);
    }    

    public function canAddSolution() {
        if ((Session::haveRight('ticket', UPDATE)) && (Session::haveRight('consumable', UPDATE))) {
            return true;
        } 
        return false;
    }
}