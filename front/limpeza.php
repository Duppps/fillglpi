<?php
include('../../../inc/includes.php');

Session::checkRight('plugin_cotrisoja_limpezas', READ);

Html::header(
    GlpiPlugin\Cotrisoja\Limpeza::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'helpdesk',
    GlpiPlugin\Cotrisoja\Limpeza::class    
);

Search::show(GlpiPlugin\Cotrisoja\Limpeza::class);

Html::footer();