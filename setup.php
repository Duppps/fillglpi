<?php

define('PLUGIN_FILLGLPI_VERSION', '2.0.0');
define("PLUGIN_FILLGLPI_MIN_GLPI_VERSION", "10.0.0");
define("PLUGIN_FILLGLPI_MAX_GLPI_VERSION", "10.0.99");
define("PLUGIN_NAME", "fillglpi");

function plugin_init_fillglpi () {
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant'][PLUGIN_NAME] = true;

    $ITILInsumo = new GlpiPlugin\FillGlpi\ITILinsumo();
    $canAddSolution = $ITILInsumo->canAddSolution();

    $ITILAlterEntity = new GlpiPlugin\FillGlpi\ITILAlterEntity();
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

    $PLUGIN_HOOKS['timeline_answer_actions'][PLUGIN_NAME] = $insumoByChamado;
    $PLUGIN_HOOKS['use_massive_action'][PLUGIN_NAME] = 1;
    $PLUGIN_HOOKS['menu_toadd'][PLUGIN_NAME] = [
        'helpdesk'  => GlpiPlugin\FillGlpi\Limpeza::class,
        'assets'    => [
            GlpiPlugin\FillGlpi\Battery::class,
            GlpiPlugin\FillGlpi\Nobreak::class
        ],
    ];
    $PLUGIN_HOOKS['item_purge'][PLUGIN_NAME] = [
        GlpiPlugin\FillGlpi\Limpeza::class => 'fillglpi_updateitem_called'
    ];

    $PLUGIN_HOOKS['item_add'][PLUGIN_NAME] = [
        \Reservation::class =>  'fillglpi_additem_called'
    ];

    $PLUGIN_HOOKS['post_item_form'][PLUGIN_NAME] = 'fillglpi_params_hook';

    Plugin::registerClass(
        GlpiPlugin\FillGlpi\Reservation::class, [
           'addtabon' => [
                \ReservationItem::class
            ]
        ]
    );

    Plugin::registerClass(
        GlpiPlugin\FillGlpi\Limpeza::class, [
           'addtabon' => [
                'Computer'
            ]
        ]
    );

    Plugin::registerClass(
        GlpiPlugin\FillGlpi\FillGlpiProfile::class, [
           'addtabon' => [
                'Profile'
            ]
        ]
    );   
    
    Plugin::registerClass(
        GlpiPlugin\FillGlpi\Battery::class, [
           'addtabon' => [
                GlpiPlugin\FillGlpi\Nobreak::class
            ]
        ]
    ); 
}

function plugin_version_fillglpi () {
    return [
        'name'           => ucfirst(PLUGIN_NAME),
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
        echo __('Installed / not configured', PLUGIN_NAME);
    }
    return false;
}
