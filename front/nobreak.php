<?php
include('../../../inc/includes.php');

Session::checkRight(GlpiPlugin\Fillglpi\Nobreak::$rightname, READ);

Html::header(
    GlpiPlugin\Fillglpi\Nobreak::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'assets',
    GlpiPlugin\Fillglpi\Nobreak::class    
);

Search::show(GlpiPlugin\Fillglpi\Nobreak::class);

Html::footer();