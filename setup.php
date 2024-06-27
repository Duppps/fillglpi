<?php

define('PLUGIN_COTRISOJA_VERSION', '2.0.0');
define("PLUGIN_COTRISOJA_MIN_GLPI_VERSION", "10.0.0");
define("PLUGIN_COTRISOJA_MAX_GLPI_VERSION", "10.0.99");
define("PLUGIN_NAME", "cotrisoja");

function plugin_init_cotrisoja () {
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant'][PLUGIN_NAME] = true;

    $ITILInsumo = new GlpiPlugin\Cotrisoja\ITILinsumo();
    $canAddSolution = $ITILInsumo->canAddSolution();

    $ITILAlterEntity = new GlpiPlugin\Cotrisoja\ITILAlterEntity();
    $canAlterEntity = $ITILAlterEntity->canAddSolution();

    $insumoByChamado = [
        'insumo' => [
            'icon'          => $ITILInsumo::getIcon(),
            'type'          => 'ITILinsumo',
            'class'         => 'ITILinsumo',
            'label'         => _x('button', 'Atribuir Insumo'),
            'short_label'   => _x('button', 'Insumo'),
            'template'      => '@cotrisoja/form_insumo_chamado.html.twig',
            'item'          => $ITILInsumo,
            'hide_in_menu'  => !$canAddSolution
        ],
        'alterEntity' => [
            'icon'          => $ITILAlterEntity::getIcon(),
            'type'          => 'ITILAlterEntity',
            'class'         => 'ITILAlterEntity',
            'label'         => _x('button', 'Alterar Entidade'),
            'short_label'   => _x('button', 'Entidade'),
            'template'      => '@cotrisoja/form_alterar_entidade.html.twig',
            'item'          => $ITILAlterEntity,
            'hide_in_menu'  => !$canAlterEntity
        ]
    ];

    $PLUGIN_HOOKS['timeline_answer_actions'][PLUGIN_NAME] = $insumoByChamado;
    $PLUGIN_HOOKS['use_massive_action'][PLUGIN_NAME] = 1;
    $PLUGIN_HOOKS['menu_toadd'][PLUGIN_NAME] = [
        'helpdesk'  => GlpiPlugin\Cotrisoja\Limpeza::class,
        'assets'    => [
            GlpiPlugin\Cotrisoja\Battery::class,
            GlpiPlugin\Cotrisoja\Nobreak::class
        ],
    ];
    $PLUGIN_HOOKS['item_purge'][PLUGIN_NAME] = [
        GlpiPlugin\Cotrisoja\Limpeza::class => 'cotrisoja_updateitem_called'
    ];

    $PLUGIN_HOOKS['item_add'][PLUGIN_NAME] = [
        \Reservation::class =>  'cotrisoja_additem_called'
    ];

    $PLUGIN_HOOKS['post_item_form'][PLUGIN_NAME] = 'cotrisoja_params_hook';

    Plugin::registerClass(
        GlpiPlugin\Cotrisoja\Reservation::class, [
           'addtabon' => [
                \ReservationItem::class
            ]
        ]
    );

    Plugin::registerClass(
        GlpiPlugin\Cotrisoja\Limpeza::class, [
           'addtabon' => [
                'Computer'
            ]
        ]
    );

    Plugin::registerClass(
        GlpiPlugin\Cotrisoja\CotrisojaProfile::class, [
           'addtabon' => [
                'Profile'
            ]
        ]
    );   
    
    Plugin::registerClass(
        GlpiPlugin\Cotrisoja\Battery::class, [
           'addtabon' => [
                GlpiPlugin\Cotrisoja\Nobreak::class
            ]
        ]
    ); 
}

function plugin_version_cotrisoja () {
    return [
        'name'           => ucfirst(PLUGIN_NAME),
        'version'        => PLUGIN_COTRISOJA_VERSION,
        'author'         => 'Ruan',
        'license'        => '',
        'homepage'       => '',
        'requirements'   => [
            'glpi' => [
                'min' => PLUGIN_COTRISOJA_MIN_GLPI_VERSION,
                'max' => PLUGIN_COTRISOJA_MAX_GLPI_VERSION,
            ]
        ]
    ];
}

function plugin_cotrisoja_check_prerequisites () {
    return true;
}

function plugin_cotrisoja_check_config ($verbose = false)
{
    if (true) { 
        return true;
    }

    if ($verbose) {
        echo __('Installed / not configured', PLUGIN_NAME);
    }
    return false;
}
