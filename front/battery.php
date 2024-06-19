<?php
include('../../../inc/includes.php');

Session::checkRight(GlpiPlugin\Cotrisoja\Battery::$rightname, READ);

Html::header(
    GlpiPlugin\Cotrisoja\Battery::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'assets',
    GlpiPlugin\Cotrisoja\Battery::class    
);

Search::show(GlpiPlugin\Cotrisoja\Battery::class);

Html::footer();