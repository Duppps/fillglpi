<?php
use GlpiPlugin\FillGlpi\BD;
use Glpi\Event;
use Session;
use ITILFollowup;

include ('../../../inc/includes.php');

Session::checkLoginUser();

$fup = new ITILFollowup();  
$conn = new BD;

$redirect = null;
$handled = false;

$track = getItemForItemtype($_POST['itemtype']);

if (isset($_POST["add"])) {
    $allEntities = BD::getEntities('WHERE id = '.$_POST['entity_']);

    if (count($allEntities) < 1) {
        Session::addMessageAfterRedirect(
            __('Selecione uma entidade alvo'),
            true,
            ERROR
        );
        Html::back();
    }

    if (!isset($_POST['content']) || $_POST['content'] == '' || $_POST['content'] == NULL){
        $where = 'WHERE id = '.$_POST['entity_'];
        $entityData = reset(BD::getEntities($where));        

        $_POST['content'] = 'Ticket alterado para '. $entityData['name'];                 
    }

    $fup->check(-1, CREATE, $_POST);           

    if ($fup->add($_POST)) {  
        Event::log(
            $fup->getField('items_id'),
            strtolower($_POST['itemtype']),
            4,
            "tracking",
            sprintf(__('%s adds a followup'), $_SESSION["glpiname"])
        );  
        
        BD::updateEntityTicket($_POST['items_id'], $_POST['entity_'], Session::getLoginUserID());      

        $redirect = $track->getFormURLWithID($fup->getField('items_id'));
        $handled = true;
    } else {
        Session::addMessageAfterRedirect(
            __('Não foi possível alterar a entidade'),
            true,
            ERROR
        );
        Html::back();
    }    

} 

if ($handled) {    
    if ($track->can($_POST["items_id"], READ)) {
        $toadd = '';

        $redirect = $track->getLinkURL() . $toadd;
    } else {
        Session::addMessageAfterRedirect(
            __('You have been redirected because you no longer have access to this ticket'),
            true,
            ERROR
        );
        $redirect = $track->getSearchURL();
    }    
}

if (null == $redirect) {
    Html::back();
} else {
    Html::redirect($redirect);
}
