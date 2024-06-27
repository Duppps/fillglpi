<?php
include('../../../inc/includes.php');

Session::checkRight('plugin_cotrisoja_limpezas', READ);

Html::header(
    GlpiPlugin\FillGlpi\Limpeza::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'helpdesk',
    GlpiPlugin\FillGlpi\Limpeza::class    
);

Search::show(GlpiPlugin\FillGlpi\Limpeza::class);

Html::footer();