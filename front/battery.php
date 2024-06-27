<?php
include('../../../inc/includes.php');

Session::checkRight(GlpiPlugin\Fillglpi\Battery::$rightname, READ);

Html::header(
    GlpiPlugin\Fillglpi\Battery::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'assets',
    GlpiPlugin\Fillglpi\Battery::class    
);

Search::show(GlpiPlugin\Fillglpi\Battery::class);

Html::footer();