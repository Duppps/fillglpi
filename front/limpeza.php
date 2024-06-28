<?php
include('../../../inc/includes.php');

Session::checkRight('plugin_fillglpi_limpezas', READ);

Html::header(
    GlpiPlugin\Fillglpi\Limpeza::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'helpdesk',
    GlpiPlugin\Fillglpi\Limpeza::class    
);

Search::show(GlpiPlugin\Fillglpi\Limpeza::class);

Html::footer();