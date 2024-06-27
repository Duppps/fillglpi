<?php
function plugin_cotrisoja_install() {
    global $DB;

    $filePath = GLPI_ROOT.'/src/Reservation.php';
    $fileRenamedOldPath = GLPI_ROOT.'/src/Reservation.bkp.php';
    $fileNewPath = GLPI_ROOT.'/plugins/cotrisoja/files/ReservationWithHook.php';
    
    rename($filePath, $fileRenamedOldPath);
    copy($fileNewPath, $filePath);

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
        $DB->doQueryOrDie($query, $DB->error());
    }

    if (!$DB->tableExists('glpi_plugin_cotrisoja_nobreakmodels')) {
        $query =  "CREATE TABLE `glpi_plugin_cotrisoja_nobreakmodels` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(255) NOT NULL,
                    `brand` VARCHAR(255) NOT NULL,                
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
        $DB->doQueryOrDie($query, $DB->error());
    }

    if (!$DB->tableExists('glpi_plugin_cotrisoja_batterymodels')) {
        $query =  "CREATE TABLE `glpi_plugin_cotrisoja_batterymodels` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(255) NOT NULL,
                    `brand` VARCHAR(255) NOT NULL,                
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
        $DB->doQueryOrDie($query, $DB->error());
    }

    if (!$DB->tableExists('glpi_plugin_cotrisoja_nobreaks')) {
        $query =  "CREATE TABLE `glpi_plugin_cotrisoja_nobreaks` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `asset_number` INT(40) UNSIGNED NOT NULL,
                    `name` VARCHAR(50) DEFAULT NULL,
                    `plugin_cotrisoja_nobreakmodels_id` INT(11) UNSIGNED NOT NULL,         
                    `locations_id` INT(11) UNSIGNED NOT NULL,              
                    PRIMARY KEY (`id`),
                    KEY `plugin_cotrisoja_nobreakmodels_id` (`plugin_cotrisoja_nobreakmodels_id`),
                    KEY `locations_id` (`locations_id`),
                    UNIQUE (`asset_number`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
        $DB->doQueryOrDie($query, $DB->error());
    }

    if (!$DB->tableExists('glpi_plugin_cotrisoja_batteries')) {
        $query =  "CREATE TABLE `glpi_plugin_cotrisoja_batteries` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `expire_date` DATE NOT NULL,
                    `name` VARCHAR(50) DEFAULT NULL,
                    `plugin_cotrisoja_batterymodels_id` INT(11) UNSIGNED NOT NULL,         
                    `plugin_cotrisoja_nobreaks_id` INT(11) UNSIGNED,                                  
                    PRIMARY KEY (`id`),
                    KEY `plugin_cotrisoja_batterymodels_id` (`plugin_cotrisoja_batterymodels_id`),
                    KEY `plugin_cotrisoja_nobreaks_id` (`plugin_cotrisoja_nobreaks_id`) 
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
        $DB->doQueryOrDie($query, $DB->error());
    }

    if (!$DB->tableExists('glpi_plugin_cotrisoja_batteries')) {
        $query =  "CREATE TABLE `glpi_plugin_cotrisoja_batteries` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `expire_date` DATE NOT NULL,
                    `name` VARCHAR(50) DEFAULT NULL,
                    `plugin_cotrisoja_batterymodels_id` INT(11) UNSIGNED NOT NULL,         
                    `plugin_cotrisoja_nobreaks_id` INT(11) UNSIGNED,                                  
                    PRIMARY KEY (`id`),
                    KEY `plugin_cotrisoja_batterymodels_id` (`plugin_cotrisoja_batterymodels_id`),
                    KEY `plugin_cotrisoja_nobreaks_id` (`plugin_cotrisoja_nobreaks_id`) 
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
        $DB->doQueryOrDie($query, $DB->error());
    }

    if (!$DB->tableExists('glpi_plugin_cotrisoja_resources')) {
        $query =  "CREATE TABLE `glpi_plugin_cotrisoja_resources` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name`  VARCHAR(55) NOT NULL,
                    `reservationitems_id` INT(10) NOT NULL,                            
                    PRIMARY KEY (`id`),
                    KEY `reservationitems_id` (`reservationitems_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
        $DB->doQueryOrDie($query, $DB->error());
    }

    if (!$DB->tableExists('glpi_plugin_cotrisoja_reservations')) {
        $query =  "CREATE TABLE `glpi_plugin_cotrisoja_reservations` (
                    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `people_quantity` INT(11),
                    `reservations_id` INT(10) UNSIGNED NOT NULL,
                    PRIMARY KEY (`id`),
                    KEY `reservations_id` (`reservations_id`),  
                    UNIQUE (`reservations_id`)     
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
        $DB->doQueryOrDie($query, $DB->error());
    }

    if (!$DB->tableExists('glpi_plugin_cotrisoja_reservations_resources')) {
        $query =  "CREATE TABLE `glpi_plugin_cotrisoja_reservations_resources` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `plugin_cotrisoja_resources_id` INT(11) UNSIGNED NOT NULL,    
                    `plugin_cotrisoja_reservations_id` INT(11) UNSIGNED NOT NULL,                             
                    PRIMARY KEY (`id`),
                    KEY `plugin_cotrisoja_resources_id` (`plugin_cotrisoja_resources_id`),
                    KEY `plugin_cotrisoja_reservations_id` (`plugin_cotrisoja_reservations_id`) 
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
        $DB->doQueryOrDie($query, $DB->error());
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

    $filePath = GLPI_ROOT.'/src/Reservation.php';
    $fileRenamedOldPath = GLPI_ROOT.'/src/Reservation.bkp.php';
    
    unlink($filePath);
    rename($fileRenamedOldPath, $filePath);   

    $tables = [
      'limpezas',
      'batteries',
      'nobreaks',
      'batterymodels',
      'nobreakmodels',
      'reservations',
      'reservations_resources',
      'resources'
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

function plugin_cotrisoja_updateitem_called(CommonDBTM $item) {
    if ($item::getType() == GlpiPlugin\Cotrisoja\Limpeza::class) {
        GlpiPlugin\Cotrisoja\Limpeza::itemPurge($item);
    }
}

function cotrisoja_additem_called(CommonDBTM $item) {
    if ($item::getType() == \Reservation::class) { 
        $obj = new GlpiPlugin\Cotrisoja\Reservation;

        if (isset($_POST['type_reserve']) && $_POST['type_reserve'] == 'unique') {
            Html::redirect($obj->getFormURLWithID($item->getID()));
        } else {            
            $_POST['reservations_id'] = $item->fields['id'];

            foreach ($_POST as $i => $key) {
                if (strpos($i, 'resource_id_') !== false) {
                    GlpiPlugin\Cotrisoja\Resource::create($key, $_POST['reservations_id']);
                }
            }  
    
            $obj->check(-1, CREATE, $_POST);
            $obj->add($_POST);
        }          
    }
}

function cotrisoja_params_hook(array $params) {
    if (($params['item'] == new \Reservation())) {
        GlpiPlugin\Cotrisoja\Reservation::addFieldsInReservationForm();
    } 
}


function plugin_cotrisoja_getDropdown() {
    return [
        GlpiPlugin\Cotrisoja\NobreakModel::class => _n('Modelo de Nobreak', 'Modelos de Nobreak', 2, 'cotrisoja'),
        GlpiPlugin\Cotrisoja\BatteryModel::class => _n('Modelo de Bateria', 'Modelos de Baterias', 2, 'cotrisoja'),
        GlpiPlugin\Cotrisoja\Resource::class     => _n('Recurso para Reserva', 'Recursos para Reserva', 2, 'cotrisoja')
    ];
 }
