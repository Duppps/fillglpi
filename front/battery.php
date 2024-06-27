<?php
include('../../../inc/includes.php');

Session::checkRight(GlpiPlugin\FillGlpi\Battery::$rightname, READ);

Html::header(
    GlpiPlugin\FillGlpi\Battery::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'assets',
    GlpiPlugin\FillGlpi\Battery::class    
);

Search::show(GlpiPlugin\FillGlpi\Battery::class);

Html::footer();