<?php
include('../../../inc/includes.php');

//Session::checkRight(GlpiPlugin\Cotrisoja\Nobreak::$rightname, READ);

Html::header(
    GlpiPlugin\Cotrisoja\Nobreak::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'assets',
    GlpiPlugin\Cotrisoja\Nobreak::class    
);

Search::show(GlpiPlugin\Cotrisoja\Nobreak::class);

Html::footer();