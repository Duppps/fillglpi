<?php
include('../../../inc/includes.php');

//Session::checkRight(GlpiPlugin\Cotrisoja\BatteryModel::$rightname, READ);

Html::header(
    GlpiPlugin\Cotrisoja\BatteryModel::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'tools',
    GlpiPlugin\Cotrisoja\BatteryModel::class    
);

Search::show(GlpiPlugin\Cotrisoja\BatteryModel::class);

Html::footer();