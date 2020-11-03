<?php
namespace inspinia\widgets;

use inspinia\widgets\datatables\DataTablesAsset;
use yii\base\Widget;
use yii\bootstrap\BootstrapWidgetTrait;
use yii\helpers\Json;
use yii\web\View;
use yii\helpers\ArrayHelper;

class DataTables extends Widget
{
    use BootstrapWidgetTrait;

    public $options = [];

    public function init()
    {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        $this->clientOptions['language'] = ArrayHelper::merge([
            "loadingRecords" => "กำลังดำเนินการ...",
            "zeroRecords" =>  "",
            "lengthMenu" =>  "แสดง _MENU_ แถว",
            "info" => "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
            "infoEmpty" => "แสดง 0 ถึง 0 จาก 0 แถว",
            "infoFiltered" => "(กรองข้อมูล _MAX_ ทุกแถว)",
            "emptyTable" => "ไม่พบข้อมูล",
            "sSearch" => "ค้นหา: ",
            "oPaginate" => [
                "sFirst" => "หน้าแรก",
                "sPrevious" => "ก่อนหน้า",
                "sNext" => "ถัดไป",
                "sLast" => "หน้าสุดท้าย"
            ],
        ],ArrayHelper::getValue($this->clientOptions,'language',[]));
    }

    public function run()
    {
        $this->registerPlugin('DataTable');
    }

    protected function registerPlugin($name)
    {
        $view = $this->getView();
        $bundle = DataTablesAsset::register($view);
        $id = $this->options['id'];
        if ($this->clientOptions !== false) {
            $dtId = str_replace('-', '', preg_replace('/(\w+) (\d+), (\d+)/i', '', $id));
            $options = empty($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
            $js = "var dt_" . $dtId . " = jQuery('#$id').$name($options);";
            $view->registerJs($js);
            $view->registerJs('$.fn.dataTable.ext.errMode = \'throw\';', View::POS_END);
        }
        $this->registerClientEvents();
    }
}
