<?php
include('../../../inc/includes.php');

//Session::checkRight(GlpiPlugin\Cotrisoja\BatteryModel::$rightname, READ);

Html::header(
    GlpiPlugin\Cotrisoja\NobreakModel::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'tools',
    GlpiPlugin\Cotrisoja\NobreakModel::class    
);

Search::show(GlpiPlugin\Cotrisoja\NobreakModel::class);

Html::footer();