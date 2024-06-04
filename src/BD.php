<?php

namespace GlpiPlugin\Cotrisoja;

class BD {
    public static function buscaTiposInsumos() {
        global $DB;

        $response = [];
        $tipos = $DB->request('SELECT * FROM glpi_consumableitemtypes');

        foreach( $tipos as $tipo ) {
            $response[] = [
                'id' => $tipo['id'],
                'nome' => $tipo['name']
            ];
        }

        return $response;
    }

    public static function buscaInsumosPorTipo($where = '') {
        global $DB;

        $response = [];              

        $insumos = $DB->request('SELECT ci.*, (SELECT COUNT(*) FROM glpi_consumables c WHERE c.consumableitems_id = ci.id AND c.items_id = 0) AS quantidade FROM glpi_consumableitems ci'.$where.'');

        foreach( $insumos as $insumo ) {
            $response[] = [
                'id'=> $insumo['id'],
                'nome'=> $insumo['name'],
                'quantidade'=> $insumo['quantidade']
            ];
        }

        return $response;
    }    

    public static function buscaInsumos($where = '') {
        global $DB;

        $response = [];
        $insumos = $DB->request('SELECT ci.*, (SELECT COUNT(*) FROM glpi_consumables c WHERE c.consumableitems_id = ci.id) AS quantidade FROM glpi_consumableitems ci '.$where.'');

        foreach( $insumos as $insumo ) {
            $response[] = [
                'id' => $insumo['id'],
                'nome' => $insumo['name'],
                'quantidade' => $insumo['quantidade']
            ];
        }

        return $response;
    }

    //pq nao é estatico?
    public function buscaInsumoDisponivelPorID($id, $qtd) {
        global $DB;

        $response = [];

        $insumos = $DB->request('SELECT * FROM glpi_consumables WHERE consumableitems_id = '.$id.' AND date_out IS NOT NULL LIMIT '.$qtd.'');

        foreach( $insumos as $insumo ) {
            $response[] = [
                'id'=> $insumo['id'],
                'iDItemInsumo'=> $insumo['consumableitems_id'],
                'date_in'=> $insumo['date_in']
            ];
        }

        return $response;
    }  
    
    public static function getEntities($where = '') {
        global $DB;

        $response = [];

        $entities = $DB->request('SELECT * FROM glpi_entities '. $where . '');

        foreach( $entities as $entity ) {           
            $response[] = [
                'id' => $entity['id'],
                'name' => $entity['name'],
                'completename' => $entity['completename'],
                'entities_id' => $entity['entities_id']
            ];
        }

        return $response;
    }    

    public static function getEntityByTicket($idTicket) {
        global $DB;

        $response = [];
        $entities = $DB->request('SELECT entities_id FROM glpi_tickets WHERE id = '.$idTicket.'');  

        foreach( $entities as $entity ) {           
            $response = [
                'entities_id' => $entity['entities_id']
            ];
        }

        $response = (int)$response['entities_id'];

        return $response;
    }

    public static function updateEntityTicket($ticketID, $entityID, $userID) {
        global $DB;

        $DB->request('UPDATE glpi_tickets SET entities_id = '.$entityID.', status =  1 WHERE id = '.$ticketID.'');     
        $DB->request('DELETE FROM glpi_tickets_users WHERE type = 2 AND tickets_id = '. $ticketID.'');  

        $queryTicketsUsers = $DB->request('SELECT * FROM glpi_tickets_users WHERE tickets_id = '.$ticketID.' AND users_id = '.$userID.' AND type = 3');
        
        if (count($queryTicketsUsers) < 1) {
            $DB->request('INSERT IGNORE INTO glpi_tickets_users (tickets_id, users_id, type, alternative_email) VALUES ('.$ticketID.', '.$userID.', 3, "")'); 
        }        
    }

    public static function getItemConsumable($itemType, $ID) {
        global $DB;

        $response = [];

        $request = $DB->request('SELECT glpi_consumables.*, (SELECT name FROM glpi_consumableitems ci WHERE ci.id = glpi_consumables.consumableitems_id) AS itemName FROM glpi_consumables WHERE itemtype = "'.$itemType.'" AND items_id = '.$ID.' AND date_out IS NOT NULL'); 

        foreach( $request as $item ) {           
            $response[] = [
                'id' => $item['id'],
                'consumableitems_id' => $item['consumableitems_id'],
                'name' => $item['itemName']
            ];
        }

        return $response;
    }

}