<?php
include('../../../inc/includes.php');

Session::checkRight(GlpiPlugin\Cotrisoja\Reservation::$rightname, READ);

Html::header(
    GlpiPlugin\Cotrisoja\Reservation::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'tools',
    \Reservation::class    
);

Search::show(GlpiPlugin\Cotrisoja\Reservation::class);

Html::footer();