<?php

function plugin_cotrisoja_install() {
    global $DB;

    $migration = new Migration(1);     

    if (!$DB->tableExists('glpi_plugin_cotrisoja_limpezas')) {
        $query =  "CREATE TABLE `glpi_plugin_cotrisoja_limpezas` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `computers_id` INT(11) UNSIGNED NOT NULL,
                    `date` DATE NOT NULL,
                    `observation` VARCHAR(500) NOT NULL,                  
                    PRIMARY KEY (`id`),
                    KEY `computers_id` (`computers_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
        $DB->queryOrDie($query, $DB->error());
    }

    if (!$DB->tableExists('glpi_plugin_cotrisoja_nobreakmodels')) {
        $query =  "CREATE TABLE `glpi_plugin_cotrisoja_nobreakmodels` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(255) NOT NULL,
                    `brand` VARCHAR(255) NOT NULL,                
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
        $DB->queryOrDie($query, $DB->error());
    }

    if (!$DB->tableExists('glpi_plugin_cotrisoja_batterymodels')) {
        $query =  "CREATE TABLE `glpi_plugin_cotrisoja_batterymodels` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(255) NOT NULL,
                    `brand` VARCHAR(255) NOT NULL,                
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
        $DB->queryOrDie($query, $DB->error());
    }

    if (!$DB->tableExists('glpi_plugin_cotrisoja_nobreaks')) {
        $query =  "CREATE TABLE `glpi_plugin_cotrisoja_nobreaks` (
                    `id` INT(11) UNSIGNED NOT NULL,
                    `name` VARCHAR(50) DEFAULT NULL,
                    `plugin_cotrisoja_nobreakmodels_id` INT(11) UNSIGNED NOT NULL,         
                    `locations_id` INT(11) UNSIGNED NOT NULL,              
                    PRIMARY KEY (`id`),
                    KEY `plugin_cotrisoja_nobreakmodels_id` (`plugin_cotrisoja_nobreakmodels_id`),
                    KEY `locations_id` (`locations_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
        $DB->queryOrDie($query, $DB->error());
    }

    if (!$DB->tableExists('glpi_plugin_cotrisoja_batteries')) {
        $query =  "CREATE TABLE `glpi_plugin_cotrisoja_batteries` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `expire_date` DATE NOT NULL,
                    `name` VARCHAR(50) DEFAULT NULL,
                    `plugin_cotrisoja_batterymodels_id` INT(11) UNSIGNED NOT NULL,         
                    `plugin_cotrisoja_nobreaks_id` INT(11) UNSIGNED NOT NULL,                                  
                    PRIMARY KEY (`id`),
                    KEY `plugin_cotrisoja_batterymodels_id` (`plugin_cotrisoja_batterymodels_id`),
                    KEY `plugin_cotrisoja_nobreaks_id` (`plugin_cotrisoja_nobreaks_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
        $DB->queryOrDie($query, $DB->error());
    }

    $migration->executeMigration();

    //TODO inserir dentro de glpi_displaypreferences os valores predefinidos da pesquisa (?)

    return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_cotrisoja_uninstall() {
    global $DB;

    $tables = [
      'limpezas',
      'batteries',
      'nobreaks',
      'batterymodels',
      'nobreakmodels'
    ];

    foreach ($tables as $table) {
        $tablename = 'glpi_plugin_cotrisoja_' . $table;

        if ($DB->tableExists($tablename)) {
            $DB->queryOrDie(
                "DROP TABLE `$tablename`",
                $DB->error()
            );
        }
    }

    return true;
}

function cotrisoja_updateitem_called(CommonDBTM $item) {
    if ($item::getType() == GlpiPlugin\Cotrisoja\Limpeza::class) {
        GlpiPlugin\Cotrisoja\Limpeza::itemPurge($item);
    }
}

function plugin_cotrisoja_getAddSearchOptionsNew($itemtype) {
    $tab = [];       

    switch ($itemtype) {
        case GlpiPlugin\Cotrisoja\Limpeza::class:
            $tab[] = [
                'id'                 => '1',
                'table'              => GlpiPlugin\Cotrisoja\Limpeza::getTable(),
                'field'              => 'id',
                'name'               => __('ID'),
                'datatype'           => 'itemlink',
                'massiveaction'      => false
            ];
        
            $tab[] = [
                'id'                 => '2',
                'table'              => 'glpi_computers',
                'field'              => 'name',
                'name'               => __('Computador'),
                'datatype'           => 'itemlink'
            ];
        
            $tab[] = [
                'id'                 => '3',
                'table'              => 'glpi_users',
                'field'              => 'name',
                'joinparams'         => [  
                    'beforejoin'  => [
                        'table'      => 'glpi_computers',
                        'field'      => 'users_id',
                        'jointype'   => 'itemtype_item',
                        'beforejoin' => [
                            'table'      => GlpiPlugin\Cotrisoja\Limpeza::getTable(),
                            'field'      => 'computers_id',
                            'jointype'   => 'itemtype_item',
                        ]
                    ]            
                ],
                'name'               => __('Usuário'),
                'datatype'           => 'itemlink'
            ];    
        
            $tab[] = [
                'id'                 => '4',
                'table'              => GlpiPlugin\Cotrisoja\Limpeza::getTable(),
                'field'              => 'date',
                'name'               => __('Data'),
                'datatype'           => 'date'
            ];
        
            $tab[] = [
                'id'                 => '5',
                'table'              => GlpiPlugin\Cotrisoja\Limpeza::getTable(),
                'field'              => 'observation',
                'name'               => __('Observação'),
                'datatype'           => 'varchar'
            ];

            break;
        
        case GlpiPlugin\Cotrisoja\Battery::class:
            $battery = new GlpiPlugin\Cotrisoja\Battery;
            $battery->searchOptionsNew();

            break;
        
        case GlpiPlugin\Cotrisoja\BatteryModel::class:
            $tab[] = [
                'id'                 => '1',
                'table'              => GlpiPlugin\Cotrisoja\BatteryModel::getTable(),
                'field'              => 'name',
                'name'               => __('Name'),
                'datatype'           => 'itemlink',
                'massiveaction'      => false
            ];   
            
            $tab[] = [
                'id'                 => '2',
                'table'              => GlpiPlugin\Cotrisoja\BatteryModel::getTable(),
                'field'              => 'brand',
                'name'               => __('Marca'),
                'datatype'           => 'itemlink',
                'massiveaction'      => true
            ];   

            break;
        
        case GlpiPlugin\Cotrisoja\NobreakModel::class:
            $nobreakModel = new GlpiPlugin\Cotrisoja\NobreakModel();
            $nobreakModel->searchOptionsNew();

            break;
        
        case GlpiPlugin\Cotrisoja\Nobreak::class:
            $nobreak = new GlpiPlugin\Cotrisoja\Nobreak();
            $nobreak->searchOptionsNew();
    
            break;
    }     
 
    return $tab;
}