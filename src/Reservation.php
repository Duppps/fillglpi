<?php

namespace GlpiPlugin\Fillglpi;

use GlpiPlugin\Fillglpi\DateFormatter;
use CommonDBTM;
use CommonGLPI;
use Session;
use Glpi\Application\View\TemplateRenderer;
use Html;

class Reservation extends CommonDBTM
{
    public static $rightname = 'plugin_fillglpi_reservations';

    public static function getTypeName($nb = 0)
    {
        return _n('Reservation', 'Reservations', $nb);
    }

    public static function getIcon()
    {
        return \Reservation::getIcon();
    }

    //always true because idk how permissions in helpdesk interface works
    public static function canCreate()
    {
        return true;
    }

    public static function showCustomSearchView()
    {
        $data = [
            'header'    =>  [
                'Item',
                'Reserva',
                'Recursos',
                'Início'
            ]
        ];

        $data['values'][] = [
            'aaa',
            'asfasf',
            'pao',
            '10/03'
        ];

        Form::showSearch($data);

        return true;
    }

    public function rawSearchOptions()
    {
        $tab[] = [
            'id'                 => '1',
            'table'              => $this::getTable(),
            'field'              => 'id',
            'name'               => __('ID'),
            'datatype'           => 'itemlink',
            'massiveaction'      => true
        ];

        $tab[] = [
            'id'                 => '3',
            'table'              => \Reservation::getTable(),
            'field'              => 'begin',
            'joinparams'         => [
                'beforejoin'  => [
                    'table'      => $this::getTable(),
                    'field'      => 'reservations_id',
                    'jointype'   => 'itemtype_item',
                ]
            ],
            'name'               => __('Reservation'),
            'datatype'           => 'itemlink',
            'massiveaction'      => true
        ];

        $tab[] = [
            'id'            =>  '4',
            'table'         =>  \ReservationItem::getTable(),
            'field'         =>  'itemtype',
            'joinparams'    =>  [
                'beforejoin'     =>  [
                    'table'      =>  \Reservation::getTable(),
                    'field'      =>  'reservationitems_id',
                    'jointype'   => 'itemtype_item',
                    'beforejoin'    =>  [
                        'table'      =>  $this::getTable(),
                        'field'      =>  'reservation_id',
                        'jointype'   => 'itemtype_item',
                    ]
                ]
            ],
            'name'               => __('Item'),
            'datatype'           => 'itemlink',
            'massiveaction'      => true
        ];

        return $tab;
    }


    public static function getRootReservation($id)
    {
        $results = Sql::getValuesByID($id, 'glpi_reservations');

        foreach ($results as $result) {
            $response = [
                'id'                    =>  $result['id'],
                'reservationitems_id'   =>  $result['reservationitems_id'],
                'begin'                 =>  $result['begin'],
                'end'                   =>  $result['end'],
                'users_id'              =>  $result['users_id'],
            ];
        }

        return $response;
    }

    public function showForm($ID, array $options = [])
    {

        //TODO FAZER COM INNER JOIN
        $idReservation = $options['idReservation'];
        $resourceName = '';

        //Pega a reserva glpi_reservations
        $reservation = $this::getRootReservation($idReservation);

        $reservationBegin = DateFormatter::formatToBr($reservation['begin']);

        //Pega os itens da reserva
        $resourceReservation = Sql::getValuesByID($reservation['reservationitems_id'], 'glpi_reservationitems');
        
        foreach ($resourceReservation as $resource) {
            $itemID = $resource['items_id'];
            $itemType = $resource['itemtype'];
        }

        $resources = Resource::getResourceByItemTypeAndCheckAvailability($reservation['reservationitems_id'], $reservation['begin'], $reservation['end']);

        if (count($resources) <= 0) {
            $res = new \Reservation();
            Html::redirect($res->getFormURLWithID($reservation['id']));
        }

        $table = getTableForItemType($itemType);

        foreach (Sql::getValuesByID($itemID, $table) as $i) {
            $resourceName = $i['name'];
        }        

        echo "
            <div class='m-3 border-bottom d-flex align-items-center'>
                <h1 class='m-0'>Informações adicionais para a reserva</h1>
                <h3 class='ms-auto m-0'>" . $resourceName . ", " . $reservationBegin . "</h3>
            </div>";

        $loader = new TemplateRenderer();
        $loader->display('@fillglpi/formRecourseAfterAdd.html.twig', [
            'reservationData'   =>  $reservation,
            'resources'         =>  $resources,
            'itemtype'          =>  $itemType
        ]);

        return true;
    }

    public static function addFieldsInReservationForm()
    {
        $loader = new TemplateRenderer();
        $loader->display('@fillglpi/reserve_item_form.html.twig');
    }

    public static function showViewTable()
    {
        $loader = new TemplateRenderer();
        $loader->display(
            '@fillglpi/showTable_form.html.twig',
            [
                'view'      => 'true',
                'columns'   =>  [
                    'Item',
                    'Usuário',
                    'Início',
                    'Fim',
                ],
                'values'    =>  Sql::getOpenReservationInfo('open')
            ]
        );
    }

    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        if ($item::getType() == \ReservationItem::getType() && Session::haveRight(self::$rightname, CREATE)) {
            return __('Visualizar Reservas', 'fillglpi');
        }
    }

    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        if ($item::getType() == \ReservationItem::getType()) {
            self::showViewTable();
        }

        return true;
    }
}
