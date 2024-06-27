<?php
include('../../../inc/includes.php');

Session::checkRight(GlpiPlugin\FillGlpi\Reservation::$rightname, READ);

Html::header(
    GlpiPlugin\FillGlpi\Reservation::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'tools',
    \Reservation::class    
);

Search::show(GlpiPlugin\FillGlpi\Reservation::class);

Html::footer();