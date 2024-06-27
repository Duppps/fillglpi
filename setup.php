<?php

define('PLUGIN_FILLGLPI_VERSION', '2.0.0');
define("PLUGIN_FILLGLPI_MIN_GLPI_VERSION", "10.0.0");
define("PLUGIN_FILLGLPI_MAX_GLPI_VERSION", "10.0.99");


function plugin_init_fillglpi () {
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['fillglpi'] = true;

    $ITILInsumo = new GlpiPlugin\Fillglpi\ITILinsumo();
    $canAddSolution = $ITILInsumo->canAddSolution();

    $ITILAlterEntity = new GlpiPlugin\Fillglpi\ITILAlterEntity();
    $canAlterEntity = $ITILAlterEntity->canAddSolution();

    $insumoByChamado = [
        'insumo' => [
            'icon'          => $ITILInsumo::getIcon(),
            'type'          => 'ITILinsumo',
            'class'         => 'ITILinsumo',
            'label'         => _x('button', 'Atribuir Insumo'),
            'short_label'   => _x('button', 'Insumo'),
            'template'      => '@fillglpi/form_insumo_chamado.html.twig',
            'item'          => $ITILInsumo,
            'hide_in_menu'  => !$canAddSolution
        ],
        'alterEntity' => [
            'icon'          => $ITILAlterEntity::getIcon(),
            'type'          => 'ITILAlterEntity',
            'class'         => 'ITILAlterEntity',
            'label'         => _x('button', 'Alterar Entidade'),
            'short_label'   => _x('button', 'Entidade'),
            'template'      => '@fillglpi/form_alterar_entidade.html.twig',
            'item'          => $ITILAlterEntity,
            'hide_in_menu'  => !$canAlterEntity
        ]
    ];

    $PLUGIN_HOOKS['timeline_answer_actions']['fillglpi'] = $insumoByChamado;
    $PLUGIN_HOOKS['use_massive_action']['fillglpi'] = 1;
    $PLUGIN_HOOKS['menu_toadd']['fillglpi'] = [
        'helpdesk'  => GlpiPlugin\Fillglpi\Limpeza::class,
        'assets'    => [
            GlpiPlugin\Fillglpi\Battery::class,
            GlpiPlugin\Fillglpi\Nobreak::class
        ],
    ];
    $PLUGIN_HOOKS['item_purge']['fillglpi'] = [
        GlpiPlugin\Fillglpi\Limpeza::class => 'fillglpi_updateitem_called'
    ];

    $PLUGIN_HOOKS['item_add']['fillglpi'] = [
        \Reservation::class =>  'fillglpi_additem_called'
    ];

    $PLUGIN_HOOKS['post_item_form']['fillglpi'] = 'fillglpi_params_hook';

    Plugin::registerClass(
        GlpiPlugin\Fillglpi\Reservation::class, [
           'addtabon' => [
                \ReservationItem::class
            ]
        ]
    );

    Plugin::registerClass(
        GlpiPlugin\Fillglpi\Limpeza::class, [
           'addtabon' => [
                'Computer'
            ]
        ]
    );

    Plugin::registerClass(
        GlpiPlugin\Fillglpi\FillglpiProfile::class, [
           'addtabon' => [
                'Profile'
            ]
        ]
    );   
    
    Plugin::registerClass(
        GlpiPlugin\Fillglpi\Battery::class, [
           'addtabon' => [
                GlpiPlugin\Fillglpi\Nobreak::class
            ]
        ]
    ); 
}

function plugin_version_fillglpi () {
    return [
        'name'           => 'FillGlpi',
        'version'        => PLUGIN_FILLGLPI_VERSION,
        'author'         => 'Ruan',
        'license'        => '',
        'homepage'       => '',
        'requirements'   => [
            'glpi' => [
                'min' => PLUGIN_FILLGLPI_MIN_GLPI_VERSION,
                'max' => PLUGIN_FILLGLPI_MAX_GLPI_VERSION,
            ]
        ]
    ];
}

function plugin_fillglpi_check_prerequisites () {
    return true;
}

function plugin_fillglpi_check_config ($verbose = false)
{
    if (true) { 
        return true;
    }

    if ($verbose) {
        echo __('Installed / not configured', 'fillglpi');
    }
    return false;
}
