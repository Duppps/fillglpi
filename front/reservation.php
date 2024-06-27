<?php
include('../../../inc/includes.php');

Session::checkRight(GlpiPlugin\Fillglpi\Reservation::$rightname, READ);

Html::header(
    GlpiPlugin\Fillglpi\Reservation::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'tools',
    \Reservation::class    
);

Search::show(GlpiPlugin\Fillglpi\Reservation::class);

Html::footer();