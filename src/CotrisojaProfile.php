<?php
namespace GlpiPlugin\Cotrisoja;

use Profile;
use Html;
use Session;
use CommonGLPI;
use GlpiPlugin\Cotrisoja\Limpeza;
use GlpiPlugin\Cotrisoja\BatteryModel;
use GlpiPlugin\Cotrisoja\NobreakModel;
use GlpiPlugin\Cotrisoja\Nobreak;
use GlpiPlugin\Cotrisoja\Battery;

class CotrisojaProfile extends Profile {
    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
        switch ($item->getType()) {
            case 'Profile':
                return self::createTabEntry(__('Cotrisoja', 'cotrisoja'));            
        }

        return '';
    }

    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
        switch ($item->getType()) {
            case 'Profile':
                $profile = new self();
                $profile->showForm($item->getField('id'));
                break;
        }
        return true;
    }

    public function showForm($profiles_id, $options = [])
    {
        if (!Session::haveRight("profile", READ)) {
            return false;
        }
        $canedit = Session::haveRight("profile", UPDATE);

        $profile = new Profile();
        $profile->getFromDB($profiles_id);

        echo "<form action='" . Profile::getFormUrl() . "' method='post'>";
        echo "<table class='tab_cadre_fixe'>";


        $title = __('Cotrisoja', 'cotrisoja');

        $profile->displayRightsChoiceMatrix(
            [
                [
                    'itemtype' => Limpeza::class,
                    'label'    => Limpeza::getTypeName(),
                    'field'    => self::getProfileNameForItemtype(Limpeza::class)
                ],
                [
                    'itemtype' => BatteryModel::class,
                    'label'    => BatteryModel::getTypeName(),
                    'field'    => self::getProfileNameForItemtype(BatteryModel::class)
                ],
                [
                    'itemtype' => NobreakModel::class,
                    'label'    => NobreakModel::getTypeName(),
                    'field'    => self::getProfileNameForItemtype(NobreakModel::class)
                ],
                [
                    'itemtype' => Nobreak::class,
                    'label'    => Nobreak::getTypeName(),
                    'field'    => self::getProfileNameForItemtype(Nobreak::class)
                ],
                [
                    'itemtype' => Battery::class,
                    'label'    => Battery::getTypeName(),
                    'field'    => self::getProfileNameForItemtype(Battery::class)
                ],

            ],
            [
                'canedit'       => $canedit,
                'default_class' => 'tab_bg_2',
                'title'         => $title
            ]
        );        

        $profile->showLegend();
        if ($canedit) {
            echo "<div class='center'>";
            echo Html::hidden('id', ['value' => $profiles_id]);
            echo Html::submit(_sx('button', 'Save'), ['name' => 'update']);
            echo "</div>\n";
            Html::closeForm();
        }
        echo "</div>";

        return true;
    }     

    public static function getProfileNameForItemtype($itemtype) {
        return preg_replace("/^glpi_/", "", getTableForItemType($itemtype));
    }
}