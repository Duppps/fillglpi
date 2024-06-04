<?php

define('PLUGIN_COTRISOJA_VERSION', '1.0.0');
define("PLUGIN_COTRISOJA_MIN_GLPI_VERSION", "10.0.0");
define("PLUGIN_COTRISOJA_MAX_GLPI_VERSION", "10.0.99");

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_cotrisoja()
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['cotrisoja'] = true;

    $ITILInsumo = new GlpiPlugin\Cotrisoja\ITILinsumo();
    $canAddSolution = $ITILInsumo->canAddSolution();

    $ITILAlterEntity = new GlpiPlugin\Cotrisoja\ITILAlterEntity();
    $canAlterEntity = $ITILAlterEntity->canAddSolution();

    //TODO use plugin default dir for template, currently its using glpi root folder
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

    $PLUGIN_HOOKS['timeline_answer_actions']['cotrisoja'] = $insumoByChamado;
    $PLUGIN_HOOKS['use_massive_action']['cotrisoja'] = 1;
    $PLUGIN_HOOKS['menu_toadd']['cotrisoja'] = ['helpdesk' => GlpiPlugin\Cotrisoja\Limpeza::class];
    $PLUGIN_HOOKS['item_purge']['cotrisoja'] = [
        GlpiPlugin\Cotrisoja\Limpeza::class => 'cotrisoja_updateitem_called'
    ];

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
}


/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_cotrisoja()
{
    return [
        'name'           => 'Cotrisoja',
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

/**
 * Check pre-requisites before install
 * OPTIONNAL, but recommanded
 *
 * @return boolean
 */
function plugin_cotrisoja_check_prerequisites()
{
    return true;
}

/**
 * Check configuration process
 *
 * @param boolean $verbose Whether to display message on failure. Defaults to false
 *
 * @return boolean
 */
function plugin_cotrisoja_check_config($verbose = false)
{
    if (true) { // Your configuration check
        return true;
    }

    if ($verbose) {
        echo __('Installed / not configured', 'cotrisoja');
    }
    return false;
}
