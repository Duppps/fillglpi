<?php
include('../../../inc/includes.php');

Session::checkRight(GlpiPlugin\FillGlpi\Nobreak::$rightname, READ);

Html::header(
    GlpiPlugin\FillGlpi\Nobreak::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'assets',
    GlpiPlugin\FillGlpi\Nobreak::class    
);

Search::show(GlpiPlugin\FillGlpi\Nobreak::class);

Html::footer();