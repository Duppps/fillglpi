<?php
use GlpiPlugin\Fillglpi\ITILInsumo;
use GlpiPlugin\Fillglpi\BD;
use Glpi\Event;
use Session;
use ITILFollowup;
use Consumable;
use ITILSolution;

include ('../../../inc/includes.php');

Session::checkLoginUser();

$fup = new ITILFollowup();
$solution = new ITILSolution();

$idTicket = $_POST['items_id'];
$content = true;

if (!isset($_POST['content']) || $_POST['content'] == '' || $_POST['content'] == NULL) {
    $msg = '';
    $content = false;
}

$insumos = explode(";", $_POST['consumables']);

$consumablesToAdd = [];

foreach ($insumos as $insumo) {
    $encontrado = false;
    foreach ($consumablesToAdd as &$id) {
        if ($id["id"] == $insumo) {
        	$id["quantidade"]++;
            $encontrado = true;
            break;
        }
    }

    if (!$encontrado) {
        $consumablesToAdd[] = [
        	"id" =>	$insumo,
        	"quantidade" => 1
        ];
    }
}

array_pop($consumablesToAdd);

$conn = new BD;
$track = getItemForItemtype($_POST['itemtype']);
$track->getFromDB($_POST['items_id']);

$redirect = null;
$handled = false;

if (isset($_POST["add"])) {
    foreach ($consumablesToAdd as $consumableAdd) {
        $idInsumo = $consumableAdd["id"];
        $quantidade = $consumableAdd["quantidade"];

        $insumosDisponiveis = $conn->buscaInsumoDisponivelPorID($idInsumo, $quantidade); 

        if ((count($insumosDisponiveis) < $quantidade) || ($quantidade <= 0)) {
            Session::addMessageAfterRedirect(
                __('Sem insumos suficientes disponíveis'),
                true,
                ERROR
            );
            Html::back();
        }

        foreach ($insumosDisponiveis as $insumo){
            $consumable = new Consumable();

            if (!$consumable->out($insumo['id'], 'Ticket', $idTicket)) {
                Session::addMessageAfterRedirect(
                    __('Erro ao atribuir insumo'),
                    true,
                    ERROR
                );
                Html::back();
            }  
        }

        if (!$content){
            $where = 'WHERE id = '.$idInsumo;
            $consumableData = reset(BD::buscaInsumos($where));
        
            if ($quantidade > 1) {                
                $msg .= "\n";
                $msg .= $quantidade . ' ' . $consumableData['nome'] . ' entregues';
            } else {
                $msg .= $consumableData['nome'] . ' entregue';
            }           
             
        }
    }
    
    $_POST['content'] = $msg;

    if (isset($_POST['solTicket']) && $_POST['solTicket'] == 'true') {
        $solution->check(-1, CREATE, $_POST);
        if (!$track->canSolve()) {
            Session::addMessageAfterRedirect(
                __('You cannot solve this item!'),
                false,
                ERROR
            );
            Html::back();
        }

        if ($solution->add($_POST)) {
            if ($_SESSION['glpibackcreated']) {
                $redirect = $track->getLinkURL();
            }
            $handled = true;
        }
    } else {
        $fup->check(-1, CREATE, $_POST);           

        if ($fup->add($_POST)) {  
            Event::log(
                $fup->getField('items_id'),
                strtolower($_POST['itemtype']),
                4,
                "tracking",
                sprintf(__('%s adds a followup'), $_SESSION["glpiname"])
            );       
            
            $redirect = $track->getFormURLWithID($fup->getField('items_id'));
            $handled = true;
        }    
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
