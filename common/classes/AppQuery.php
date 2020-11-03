<?php
namespace common\classes;

use inspinia\widgets\tablecolumn\ActionTable;
use inspinia\widgets\tablecolumn\ColumnData;
use kartik\icons\Icon;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\modules\app\models\TbSound;
use inspinia\utils\CoreUtility;
use frontend\modules\app\models\TbCounterService;
use frontend\modules\app\models\TbService;
use frontend\modules\app\models\TbQtrans;
use frontend\modules\app\models\TbCaller;
use frontend\modules\app\models\TbQue;

class AppQuery
{
    #รายการคิว
    public static function getDataQueList()
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_que.que_ids',
                'tb_que.que_num',
                'tb_que.que_vn',
                'tb_que.que_hn',
                'tb_que.pt_name',
                'tb_que.service_id',
                'tb_que.service_group_id',
                'tb_que.que_status',
                'tb_que.created_at',
                'DATE_FORMAT(DATE_ADD(tb_que.created_at, INTERVAL 543 YEAR),\'%H:%i:%s\') AS created_time',
                'tb_que_status.que_status_name',
                'tb_service.service_name',
                'tb_service_group.service_group_name',
            ])
            ->from('tb_que')
            ->innerJoin('tb_que_status', 'tb_que_status.que_status_id = tb_que.que_status')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->innerJoin('tb_service_group', 'tb_service_group.service_group_id = tb_service.service_group_id')
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'que_ids',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'que_ids',
                ],
                [
                    'attribute' => 'que_num',
                ],
                [
                    'attribute' => 'que_num_badge',
                    'value' => function ($model, $key, $index, $column) {
                        return \kartik\helpers\Html::badge($model['que_num'], ['class' => 'badge badge-success', 'style' => 'font-size: 20px;font-weight: 600;width: 80px;']);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'que_vn',
                ],
                [
                    'attribute' => 'que_hn',
                ],
                [
                    'attribute' => 'pt_name',
                ],
                [
                    'attribute' => 'service_id',
                ],
                [
                    'attribute' => 'service_group_id',
                ],
                [
                    'attribute' => 'que_status',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function ($model, $key, $index) {
                        return Html::tag('code', Yii::$app->formatter->asDate($model['created_at'], 'php:H:i:s'));
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_time',
                ],
                [
                    'attribute' => 'service_name',
                ],
                [
                    'attribute' => 'service_group_name',
                ],
                [
                    'attribute' => 'que_status_name',
                    'value' => function ($model, $key, $index, $column) {
                        return AppQuery::getQueStatus($model);
                        /* if ($model['que_status'] == 1) {
                            return \kartik\helpers\Html::badge($model['que_status_name'], ['class' => 'badge badge-success', 'style' => 'width: 90px;']);
                        }
                        if ($model['que_status'] == 2) {
                            return \kartik\helpers\Html::badge($model['que_status_name'], ['class' => 'badge badge-danger', 'style' => 'width: 90px;']);
                        }
                        if ($model['que_status'] == 3) {
                            return \kartik\helpers\Html::badge($model['que_status_name'], ['class' => 'badge badge-default', 'style' => 'width: 90px;']);
                        }
                        if ($model['que_status'] == 4) {
                            return \kartik\helpers\Html::badge($model['que_status_name'], ['class' => 'badge badge-warning', 'style' => 'width: 90px;']);
                        }
                        if ($model['que_status'] == 5) {
                            return \kartik\helpers\Html::badge($model['que_status_name'], ['class' => 'badge badge-primary', 'style' => 'width: 90px;']);
                        } */
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'status_name',
                    'value' => function ($model, $key, $index, $column) {
                        return $model['que_status_name'];
                    }
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{print} {update} {delete}',
                    'updateOptions' => [
                        'role' => 'modal-remote',
                        'style' => 'font-size: 2em;',
                        'title' => 'แก้ไข',
                    ],
                    'deleteOptions' => [
                        'class' => 'text-danger activity-delete',
                        'style' => 'font-size: 2em;',
                        'title' => 'ลบ',
                    ],
                    'buttons' => [
                        'print' => function ($url, $model, $key) {
                            return Html::a(Icon::show('print', ['style' => 'font-size: 2em;'], Icon::BSG), $url, ['target' => '_blank','title' => 'พิมพ์บัตรคิว',]);
                        },
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action == 'print') {
                            return Url::to(['/app/kiosk/print-ticket', 'que_ids' => $key]);
                        }
                        if ($action == 'update') {
                            return Url::to(['/app/kiosk/update-que', 'id' => $key]);
                        }
                        if ($action == 'delete') {
                            return Url::to(['/app/kiosk/delete-que', 'id' => $key]);
                        }
                    },
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #รายการคิว
    public static function getDataQue()
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_que.que_ids',
                'tb_que.que_num',
                'tb_que.que_vn',
                'tb_que.que_hn',
                'tb_que.pt_name',
                'tb_que.service_id',
                'tb_que.service_group_id',
                'tb_que.que_status',
                'tb_que.created_at',
                'DATE_FORMAT(DATE_ADD(tb_que.created_at, INTERVAL 543 YEAR),\'%H:%i:%s\') AS created_time',
                'tb_que_status.que_status_name',
                'tb_service.service_name',
                'tb_service_group.service_group_name',
            ])
            ->from('tb_que')
            ->innerJoin('tb_que_status', 'tb_que_status.que_status_id = tb_que.que_status')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->innerJoin('tb_service_group', 'tb_service_group.service_group_id = tb_service.service_group_id')
            ->limit(20)
            ->orderBy('tb_que.que_ids ASC')
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'key' => 'que_ids',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'que_ids',
                ],
                [
                    'attribute' => 'que_num',
                ],
                [
                    'attribute' => 'que_num_badge',
                    'value' => function ($model, $key, $index, $column) {
                        return \kartik\helpers\Html::badge($model['que_num'], ['class' => 'badge badge-success', 'style' => 'font-size: 14px;font-weight: 600;width: 80px;']);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'que_vn',
                ],
                [
                    'attribute' => 'que_hn',
                ],
                [
                    'attribute' => 'pt_name',
                ],
                [
                    'attribute' => 'service_id',
                ],
                [
                    'attribute' => 'service_group_id',
                ],
                [
                    'attribute' => 'que_status',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function ($model, $key, $index) {
                        return Html::tag('code', Yii::$app->formatter->asDate($model['created_at'], 'php:H:i:s'));
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_time',
                ],
                [
                    'attribute' => 'service_name',
                ],
                [
                    'attribute' => 'service_group_name',
                ],
                [
                    'attribute' => 'que_status_name',
                    'value' => function ($model, $key, $index, $column) {
                        return AppQuery::getQueStatus($model);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'status_name',
                    'value' => function ($model, $key, $index, $column) {
                        return $model['que_status_name'];
                    }
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{print} {update} {delete}',
                    'updateOptions' => [
                        'role' => 'modal-remote',
                        'style' => 'font-size: 14px;',
                    ],
                    'deleteOptions' => [
                        'class' => 'text-danger activity-delete',
                        'style' => 'font-size: 14px;',
                    ],
                    'buttons' => [
                        'print' => function ($url, $model, $key) {
                            return Html::a(Icon::show('print'), $url, ['target' => '_blank', 'style' => 'font-size: 18px;']);
                        },
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action == 'print') {
                            return Url::to(['/app/kiosk/print-ticket', 'que_ids' => $key]);
                        }
                        if ($action == 'update') {
                            return Url::to(['/app/kiosk/update-que', 'id' => $key]);
                        }
                        if ($action == 'delete') {
                            return Url::to(['/app/kiosk/delete-que', 'id' => $key]);
                        }
                    },
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #รายการคิว
    public static function LoadmoreQueData($qids)
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_que.que_ids',
                'tb_que.que_num',
                'tb_que.que_vn',
                'tb_que.que_hn',
                'tb_que.pt_name',
                'tb_que.service_id',
                'tb_que.service_group_id',
                'tb_que.que_status',
                'tb_que.created_at',
                'DATE_FORMAT(DATE_ADD(tb_que.created_at, INTERVAL 543 YEAR),\'%H:%i:%s\') AS created_time',
                'tb_que_status.que_status_name',
                'tb_service.service_name',
                'tb_service_group.service_group_name',
            ])
            ->from('tb_que')
            ->where('tb_que.que_ids > :que_ids', [':que_ids' => $qids])
            ->innerJoin('tb_que_status', 'tb_que_status.que_status_id = tb_que.que_status')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->innerJoin('tb_service_group', 'tb_service_group.service_group_id = tb_service.service_group_id')
            ->limit(20)
            ->orderBy('tb_que.que_ids ASC')
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'key' => 'que_ids',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'que_ids',
                ],
                [
                    'attribute' => 'que_num',
                ],
                [
                    'attribute' => 'que_num_badge',
                    'value' => function ($model, $key, $index, $column) {
                        return \kartik\helpers\Html::badge($model['que_num'], ['class' => 'badge badge-success', 'style' => 'font-size: 14px;font-weight: 600;width: 80px;']);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'que_vn',
                ],
                [
                    'attribute' => 'que_hn',
                ],
                [
                    'attribute' => 'pt_name',
                ],
                [
                    'attribute' => 'service_id',
                ],
                [
                    'attribute' => 'service_group_id',
                ],
                [
                    'attribute' => 'que_status',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function ($model, $key, $index) {
                        return Html::tag('code', Yii::$app->formatter->asDate($model['created_at'], 'php:H:i:s'));
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_time',
                ],
                [
                    'attribute' => 'service_name',
                ],
                [
                    'attribute' => 'service_group_name',
                ],
                [
                    'attribute' => 'que_status_name',
                    'value' => function ($model, $key, $index, $column) {
                        if ($model['que_status'] == 1) {
                            return \kartik\helpers\Html::badge($model['que_status_name'], ['class' => 'badge badge-success', 'style' => 'width: 90px;']);
                        }
                        if ($model['que_status'] == 2) {
                            return \kartik\helpers\Html::badge($model['que_status_name'], ['class' => 'badge badge-danger', 'style' => 'width: 90px;']);
                        }
                        if ($model['que_status'] == 3) {
                            return \kartik\helpers\Html::badge($model['que_status_name'], ['class' => 'badge badge-default', 'style' => 'width: 90px;']);
                        }
                        if ($model['que_status'] == 4) {
                            return \kartik\helpers\Html::badge($model['que_status_name'], ['class' => 'badge badge-warning', 'style' => 'width: 90px;']);
                        }
                        if ($model['que_status'] == 5) {
                            return \kartik\helpers\Html::badge($model['que_status_name'], ['class' => 'badge badge-primary', 'style' => 'width: 90px;']);
                        }
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'status_name',
                    'value' => function ($model, $key, $index, $column) {
                        return $model['que_status_name'];
                    }
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{print} {update} {delete}',
                    'updateOptions' => [
                        'role' => 'modal-remote',
                        'style' => 'font-size: 14px;',
                    ],
                    'deleteOptions' => [
                        'class' => 'text-danger activity-delete',
                        'style' => 'font-size: 14px;',
                    ],
                    'buttons' => [
                        'print' => function ($url, $model, $key) {
                            return Html::a(Icon::show('print'), $url, ['target' => '_blank', 'style' => 'font-size: 18px;']);
                        },
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action == 'print') {
                            return Url::to(['/app/kiosk/print-ticket', 'que_ids' => $key]);
                        }
                        if ($action == 'update') {
                            return Url::to(['/app/kiosk/update-que', 'id' => $key]);
                        }
                        if ($action == 'delete') {
                            return Url::to(['/app/kiosk/delete-que', 'id' => $key]);
                        }
                    },
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #ข้อมูลกลุ่มบริการ
    public static function getDataServiceGroup()
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_service_group.service_group_id',
                'tb_service_group.service_group_name',
                'tb_service_group.service_group_status',
                'tb_service.service_id',
                'tb_service.service_name',
                'tb_service.print_template_id',
                'tb_service.print_copy_qty',
                'tb_service.service_prefix',
                'tb_service.service_numdigit',
                'tb_service.service_status',
                'tb_ticket.*',
            ])
            ->from('tb_service_group')
            ->leftJoin('tb_service', 'tb_service.service_group_id = tb_service_group.service_group_id')
            ->leftJoin('tb_ticket', 'tb_ticket.ids = tb_service.print_template_id')
            ->orderBy('tb_service_group.service_group_id ASC')
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'service_group_id',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'service_group_id',
                ],
                [
                    'attribute' => 'service_group_name',
                ],
                [
                    'attribute' => 'service_group_status',
                ],
                [
                    'attribute' => 'service_id',
                ],
                [
                    'attribute' => 'service_name',
                ],
                [
                    'attribute' => 'service_group_id',
                ],
                [
                    'attribute' => 'print_template_id',
                ],
                [
                    'attribute' => 'print_copy_qty',
                ],
                [
                    'attribute' => 'service_prefix',
                ],
                [
                    'attribute' => 'service_numdigit',
                ],
                [
                    'attribute' => 'service_status',
                    'value' => function ($model, $key, $index) {
                        return AppQuery::getBadgeStatus($model['service_status']);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'hos_name_th',
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{update} {delete}',
                    'updateOptions' => [
                        'role' => 'modal-remote',
                        'style' => 'font-size: 2em;',
                        'title' => 'แก้ไข',
                    ],
                    'deleteOptions' => [
                        'class' => 'text-danger activity-delete',
                        'style' => 'font-size: 2em;',
                        'title' => 'ลบ',
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action == 'update') {
                            return Url::to(['/app/setting/update-service-group', 'id' => $key]);
                        }
                        if ($action == 'delete') {
                            return Url::to(['/app/setting/delete-service-group', 'id' => $key, 'service_id' => $model['service_id']]);
                        }
                    },
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #ข้อมูลบัตรคิว
    public static function getDataTicket()
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_ticket.*',
            ])
            ->from('tb_ticket')
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'ids',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'ids',
                ],
                [
                    'attribute' => 'hos_name_th',
                ],
                [
                    'attribute' => 'hos_name_en',
                ],
                [
                    'attribute' => 'barcode_type',
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($model, $key, $index) {
                        return AppQuery::getBadgeStatus($model['status']);
                    },
                    'format' => 'raw',
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{update} {delete}',
                    'updateOptions' => [
                        'style' => 'font-size: 2em;',
                        'title' => 'แก้ไข',
                    ],
                    'deleteOptions' => [
                        'class' => 'text-danger activity-delete',
                        'style' => 'font-size: 2em;',
                        'title' => 'ลบ',
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action == 'update') {
                            return Url::to(['/app/setting/update-ticket', 'id' => $key]);
                        }
                        if ($action == 'delete') {
                            return Url::to(['/app/setting/delete-ticket', 'id' => $key]);
                        }
                    },
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #ข้อมูลจุดบริการ
    public static function getDataCounterService()
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_counter_service_type.counter_service_type_id',
                'tb_counter_service_type.counter_service_type_name',
                'tb_counter_service.counter_service_name',
                'tb_counter_service.counter_service_call_number',
                'tb_service_group.service_group_name',
                'tb_counter_service.counter_service_id',
                'tb_counter_service.sound_station_id',
                'tb_counter_service.sound_id',
                'tb_counter_service.sound_service_id',
                'tb_counter_service.counter_service_order',
                'tb_counter_service.counter_service_status',
            ])
            ->from('tb_counter_service_type')
            ->leftJoin('tb_counter_service', 'tb_counter_service.counter_service_type_id = tb_counter_service_type.counter_service_type_id')
            ->leftJoin('tb_service_group', 'tb_service_group.service_group_id = tb_counter_service.service_group_id')
            ->orderBy('counter_service_type_id ASC')
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'counter_service_type_id',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'counter_service_type_id',
                ],
                [
                    'attribute' => 'counter_service_type_name',
                ],
                [
                    'attribute' => 'counter_service_id',
                ],
                [
                    'attribute' => 'counter_service_name',
                ],
                [
                    'attribute' => 'counter_service_call_number',
                ],
                [
                    'attribute' => 'service_group_name',
                ],
                [
                    'attribute' => 'sound_station_id',
                ],
                [
                    'attribute' => 'sound_name1',
                    'value' => function ($model, $key, $index) {
                        return AppQuery::getSoundname($model['sound_service_id']);
                    },
                ],
                [
                    'attribute' => 'sound_name2',
                    'value' => function ($model, $key, $index) {
                        return AppQuery::getSoundname($model['sound_id']);
                    },
                ],
                [
                    'attribute' => 'counter_service_status',
                    'value' => function ($model, $key, $index) {
                        return AppQuery::getBadgeStatus($model['counter_service_status']);
                    },
                    'format' => 'raw',
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{update} {delete}',
                    'updateOptions' => [
                        'role' => 'modal-remote',
                        'style' => 'font-size: 2em;',
                        'title' => 'แก้ไข',
                    ],
                    'deleteOptions' => [
                        'class' => 'text-danger activity-delete',
                        'style' => 'font-size: 2em;',
                        'title' => 'ลบ',
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action == 'update') {
                            return Url::to(['/app/setting/update-counter-service', 'id' => $key]);
                        }
                        if ($action == 'delete') {
                            return Url::to(['/app/setting/delete-counter-service', 'counter_service_type_id' => $key, 'counter_service_id' => $model['counter_service_id']]);
                        }
                    },
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #ข้อมูลไฟล์เสียง
    public static function getDataSound(){
        $query = (new \yii\db\Query())
            ->select([
                'tb_sound.*'
            ])
            ->from('tb_sound')
            ->orderBy('sound_type ASC')
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'sound_id',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'sound_id',
                ],
                [
                    'attribute' => 'sound_name',
                ],
                [
                    'attribute' => 'sound_path_name',
                ],
                [
                    'attribute' => 'sound_th',
                ],
                [
                    'attribute' => 'sound_type',
                    'value' => function($model,$key,$index){
                        return $model['sound_type']  == 1 ? 'เสียงผู้หญิง' : 'เสียงผู้ชาย';
                    },
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{update} {delete}',
                    'updateOptions' => [
                        'role' => 'modal-remote',
                        'style' => 'font-size: 2em;',
                        'title' => 'แก้ไข',
                    ],
                    'deleteOptions' => [
                        'class' => 'text-danger activity-delete',
                        'style' => 'font-size: 2em;',
                        'title' => 'ลบ',
                    ],
                    'urlCreator' => function ( $action, $model, $key, $index) {
                        if($action == 'update'){
                            return Url::to(['/app/setting/update-sound','id' => $key]);
                        }
                        if($action == 'delete'){
                            return Url::to(['/app/setting/delete-sound','id' => $key]);
                        }
                    }
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #ข้อมูลโปรแกรมเสียง
    public static function getDataSoundStation()
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_sound_station.*'
            ])
            ->from('tb_sound_station')
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'sound_station_id',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'sound_station_id',
                ],
                [
                    'attribute' => 'sound_station_name',
                ],
                [
                    'attribute' => 'counter_service_id',
                    'value' => function($model,$key,$index){
                        return AppQuery::getCounterServiceName($model['counter_service_id']);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'sound_station_status',
                    'value' => function($model,$key,$index){
                        return AppQuery::getBadgeStatus($model['sound_station_status']);
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{update} {delete}',
                    'updateOptions' => [
                        'role' => 'modal-remote',
                        'style' => 'font-size: 2em;',
                        'title' => 'แก้ไข',
                    ],
                    'deleteOptions' => [
                        'class' => 'text-danger activity-delete',
                        'style' => 'font-size: 2em;',
                        'title' => 'ลบ',
                    ],
                    'urlCreator' => function ( $action, $model, $key, $index) {
                        if($action == 'update'){
                            return Url::to(['/app/setting/update-sound-station','id' => $key]);
                        }
                        if($action == 'delete'){
                            return Url::to(['/app/setting/delete-sound-station','id' => $key]);
                        }
                    }
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #ข้อมูลเซอร์วิสโปรไฟล์
    public static function getDataServiceProfile()
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_service_profile.*',
                'tb_counter_service_type.*'
            ])
            ->from('tb_service_profile')
            ->innerJoin('tb_counter_service_type','tb_counter_service_type.counter_service_type_id = tb_service_profile.counter_service_type_id')
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'service_profile_id',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'service_profile_id',
                ],
                [
                    'attribute' => 'service_profile_name',
                ],
                [
                    'attribute' => 'counter_service_type_name',
                ],
                [
                    'attribute' => 'service_names',
                    'value' => function($model,$key,$index){
                        return AppQuery::getServiceNames($model['service_id']);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'service_profile_status',
                    'value' => function($model,$key,$index){
                        return AppQuery::getBadgeStatus($model['service_profile_status']);
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{update} {delete}',
                    'updateOptions' => [
                        'role' => 'modal-remote',
                        'style' => 'font-size: 2em;',
                        'title' => 'แก้ไข',
                    ],
                    'deleteOptions' => [
                        'class' => 'text-danger activity-delete',
                        'style' => 'font-size: 2em;',
                        'title' => 'ลบ',
                    ],
                    'urlCreator' => function ( $action, $model, $key, $index) {
                        if($action == 'update'){
                            return Url::to(['/app/setting/update-service-profile','id' => $key]);
                        }
                        if($action == 'delete'){
                            return Url::to(['/app/setting/delete-service-profile','id' => $key]);
                        }
                    }
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #ข้อมูลจอแสดงผล
    public static function getDataDisplay()
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_display.*'
            ])
            ->from('tb_display')
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'display_ids',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'display_ids',
                ],
                [
                    'attribute' => 'display_name',
                ],
                [
                    'attribute' => 'display_status',
                    'value' => function($model,$key,$index){
                        return AppQuery::getBadgeStatus($model['display_status']);
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{duplicate} {update} {delete}',
                    'updateOptions' => [
                        'style' => 'font-size: 2em;',
                        'title' => 'แก้ไข',
                    ],
                    'deleteOptions' => [
                        'class' => 'text-danger activity-delete',
                        'style' => 'font-size: 2em;',
                        'title' => 'ลบ',
                    ],
                    'buttons' => [
                        'duplicate' => function($url, $model, $key){
                            return Html::a(Icon::show('copy'),['/app/setting/copy-display','id' => $key],['class' => 'activity-copy','title' => 'Duplicate','style' => 'font-size: 2em;',]);
                        }
                    ],
                    'urlCreator' => function ( $action, $model, $key, $index) {
                        if($action == 'update'){
                            return Url::to(['/app/setting/update-display','id' => $key]);
                        }
                        if($action == 'delete'){
                            return Url::to(['/app/setting/delete-display','id' => $key]);
                        }
                    }
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #ข้อมูลรายการคิว ตรวจสอบยา
    public static function getDataWaitCheckdrug($bodyParams)
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_que.que_ids',
                'tb_que.que_num',
                'tb_que.service_id',
                'tb_que.service_group_id',
                'tb_que.que_status',
                'tb_que_status.que_status_name',
                'tb_service.service_name',
                'tb_service_group.service_group_name',
                'tb_que.created_at',
                'DATE_FORMAT(DATE_ADD(tb_que.created_at, INTERVAL 543 YEAR),\'%H:%i:%s\') AS created_time',
                'tb_service.service_prefix'
            ])
            ->from('tb_que')
            ->where([
                'tb_que.que_status' => 1,
                'tb_que.service_id' => $bodyParams['service_id']
            ])
            ->innerJoin('tb_que_status', 'tb_que_status.que_status_id = tb_que.que_status')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->innerJoin('tb_service_group', 'tb_service_group.service_group_id = tb_service.service_group_id')
            ->orderBy(['tb_que.created_at' => SORT_ASC])
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'que_ids',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'que_ids',
                ],
                [
                    'attribute' => 'que_num',
                ],
                [
                    'attribute' => 'que_num_badge',
                    'value' => function($model, $key, $index, $column){
                        return \kartik\helpers\Html::badge($model['que_num'],['class' => 'badge badge-success','style' => 'width: 80px;font-size: 20px;font-weight: 600;']);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'service_id',
                ],
                [
                    'attribute' => 'service_group_id',
                ],
                [
                    'attribute' => 'que_status',
                ],
                [
                    'attribute' => 'service_name',
                ],
                [
                    'attribute' => 'service_group_name',
                ],
                [
                    'attribute' => 'que_status_name',
                ],
                [
                    'attribute' => 'service_prefix',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function($model, $key, $index, $column){
                        return Html::tag('code',Yii::$app->formatter->asDate($model['created_at'], 'php:H:i:s'));
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_time',
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{not-payment} {payment} {waiting-drug} {delete}',
                    'deleteOptions' => [
                        'style' => 'color: rgb(236, 71, 88) !important;font-size: 1.5em;',
                        'icon' => Icon::show('hand-pointer-o')
                    ],
                    'dropdown' => true,
                    'dropdownButton' => [
                        'class' => 'btn btn-primary btn-outline btn-lg',
                        'label' => 'ดำเนินการ',
                    ],
                    'dropdownOptions' => [
                        'class' => 'dropdown dropdown-action'
                    ],
                    'buttons' => [
                        'not-payment' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' ไม่ชำระเงิน',false,[
                                'style' => 'color: rgb(26, 179, 148) !important;font-size: 1.5em;',
                                'class' => 'activity-not-payment',
                                'data-url' => $url,
                            ]),[]);
                        },
                        'payment' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' ชำระเงิน',false,[
                                'style' => 'color: rgb(248, 172, 89) !important;font-size: 1.5em;',
                                'class' => 'activity-payment',
                                'data-url' => $url,
                            ]));
                        },
                        'waiting-drug' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' รอยานาน',false,[
                                'class' => 'activity-waiting-drug',
                                'data-url' => $url,
                                'style' => 'font-size: 1.5em;'
                            ]),[]);
                        },
                    ],
                    
                    'urlCreator' => function ( $action, $model, $key, $index) {
                        if($action == 'payment'){
                            return Url::to(['/app/calling/update-status-checkdrug','que_ids' => $key]);
                        }
                        if($action == 'not-payment'){
                            return Url::to(['/app/calling/update-status-checkdrug','que_ids' => $key]);
                        }
                        if($action == 'waiting-drug'){
                            return Url::to(['/app/calling/update-status-checkdrug','que_ids' => $key]);
                        }
                        if($action == 'delete'){
                            return Url::to(['/app/kiosk/delete-que','id' => $key]);
                        }
                    }
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #คิวรอยานาน ตรวจสอบยา
    public static function getDataWaitDrugCheckdrug($bodyParams)
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_que.que_ids',
                'tb_que.que_num',
                'tb_que.service_id',
                'tb_que.service_group_id',
                'tb_que.que_status',
                'tb_que_status.que_status_name',
                'tb_service.service_name',
                'tb_service_group.service_group_name',
                'tb_que.created_at',
                'DATE_FORMAT(DATE_ADD(tb_que.created_at, INTERVAL 543 YEAR),\'%H:%i:%s\') AS created_time',
                'tb_service.service_prefix'
            ])
            ->from('tb_que')
            ->where([
                'tb_que.que_status' => 2,
                'tb_que.service_id' => $bodyParams['service_id']
            ])
            ->innerJoin('tb_que_status', 'tb_que_status.que_status_id = tb_que.que_status')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->innerJoin('tb_service_group', 'tb_service_group.service_group_id = tb_service.service_group_id')
            ->orderBy(['tb_que.created_at' => SORT_ASC])
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'que_ids',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'que_ids',
                ],
                [
                    'attribute' => 'que_num',
                ],
                [
                    'attribute' => 'que_num_badge',
                    'value' => function($model, $key, $index, $column){
                        return \kartik\helpers\Html::badge($model['que_num'],['class' => 'badge','style' => 'width: 80px;font-size: 20px;font-weight: 600;']);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'service_id',
                ],
                [
                    'attribute' => 'service_group_id',
                ],
                [
                    'attribute' => 'que_status',
                ],
                [
                    'attribute' => 'service_name',
                ],
                [
                    'attribute' => 'service_group_name',
                ],
                [
                    'attribute' => 'que_status_name',
                ],
                [
                    'attribute' => 'service_prefix',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function($model, $key, $index, $column){
                        return Html::tag('code',Yii::$app->formatter->asDate($model['created_at'], 'php:H:i:s'));
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_time',
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{not-payment} {payment} {delete}',
                    'deleteOptions' => [
                        'style' => 'color: rgb(236, 71, 88) !important;font-size: 1.5em;',
                        'icon' => Icon::show('hand-pointer-o')
                    ],
                    'dropdown' => true,
                    'dropdownButton' => [
                        'class' => 'btn btn-primary btn-outline btn-lg',
                        'label' => 'ดำเนินการ'
                    ],
                    'dropdownOptions' => [
                        'class' => 'dropdown dropdown-action'
                    ],
                    'buttons' => [
                        'not-payment' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' ไม่ชำระเงิน',false,[
                                'style' => 'color: rgb(26, 179, 148) !important;font-size: 1.5em;',
                                'class' => 'activity-not-payment',
                                'data-url' => $url,
                            ]),[]);
                        },
                        'payment' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' ชำระเงิน',false,[
                                'style' => 'color: rgb(248, 172, 89) !important;font-size: 1.5em;',
                                'class' => 'activity-payment',
                                'data-url' => $url,
                            ]));
                        },
                    ],
                    
                    'urlCreator' => function ( $action, $model, $key, $index) {
                        if($action == 'payment'){
                            return Url::to(['/app/calling/update-status-checkdrug','que_ids' => $key]);
                        }
                        if($action == 'not-payment'){
                            return Url::to(['/app/calling/update-status-checkdrug','que_ids' => $key]);
                        }
                        if($action == 'delete'){
                            return Url::to(['/app/kiosk/delete-que','id' => $key]);
                        }
                    }
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #คิวรอชำระเงิน ตรวจสอบยา
    public static function getDataWaitPaymentCheckdrug($bodyParams)
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_que.que_ids',
                'tb_que.que_num',
                'tb_que.service_id',
                'tb_que.service_group_id',
                'tb_que.que_status',
                'tb_que_status.que_status_name',
                'tb_service.service_name',
                'tb_service_group.service_group_name',
                'tb_que.created_at',
                'DATE_FORMAT(DATE_ADD(tb_que.created_at, INTERVAL 543 YEAR),\'%H:%i:%s\') AS created_time',
                'tb_qtrans.que_trans_ids',
                'tb_qtrans.que_trans_type',
                'tb_service.service_prefix'
            ])
            ->from('tb_que')
            ->where([
                'tb_que.que_status' => 3,
                'tb_que.service_id' => $bodyParams['service_id']
            ])
            ->andWhere('tb_caller.caller_ids IS NULL')
            ->innerJoin('tb_que_status', 'tb_que_status.que_status_id = tb_que.que_status')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->innerJoin('tb_service_group', 'tb_service_group.service_group_id = tb_service.service_group_id')
            ->innerJoin('tb_qtrans', 'tb_qtrans.que_ids = tb_que.que_ids')
            ->leftJoin('tb_caller', 'tb_caller.que_trans_ids = tb_qtrans.que_trans_ids')
            ->orderBy(['tb_que.created_at' => SORT_ASC])
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'que_ids',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'que_ids',
                ],
                [
                    'attribute' => 'que_num',
                ],
                [
                    'attribute' => 'que_num_badge',
                    'value' => function($model, $key, $index, $column){
                        return \kartik\helpers\Html::badge($model['que_num'],['class' => 'badge badge-warning','style' => 'width: 80px;font-size: 20px;font-weight: 600;']);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'service_id',
                ],
                [
                    'attribute' => 'service_group_id',
                ],
                [
                    'attribute' => 'que_status',
                ],
                [
                    'attribute' => 'service_name',
                ],
                [
                    'attribute' => 'service_group_name',
                ],
                [
                    'attribute' => 'que_status_name',
                ],
                [
                    'attribute' => 'que_trans_ids',
                ],
                [
                    'attribute' => 'service_prefix',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function($model, $key, $index, $column){
                        return Html::tag('code',Yii::$app->formatter->asDate($model['created_at'], 'php:H:i:s'));
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_time'
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #รายการคิวรอเรียก ชำระเงิน
    public static function getDataWaitingPayment($bodyParams)
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_que.que_ids',
                'tb_que.que_num',
                'tb_que.service_id',
                'tb_que.service_group_id',
                'tb_que.que_status',
                'tb_que_status.que_status_name',
                'tb_service.service_name',
                'tb_service_group.service_group_name',
                'tb_que.created_at',
                'DATE_FORMAT(DATE_ADD(tb_que.created_at, INTERVAL 543 YEAR),\'%H:%i:%s\') AS created_time',
                'tb_qtrans.que_trans_ids',
                'tb_qtrans.que_trans_type',
                'tb_caller.caller_ids',
                'tb_caller.call_timestp',
                'tb_service.service_prefix'
            ])
            ->from('tb_que')
            ->where([
                'tb_que.que_status' => 3,
                'tb_que.service_id' => $bodyParams['service_id'],
                'que_trans_status' => 0,
                'que_trans_type' => 1
            ])
            ->andWhere('tb_caller.caller_ids IS NULL')
            ->innerJoin('tb_que_status', 'tb_que_status.que_status_id = tb_que.que_status')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->innerJoin('tb_service_group', 'tb_service_group.service_group_id = tb_service.service_group_id')
            ->innerJoin('tb_qtrans', 'tb_qtrans.que_ids = tb_que.que_ids')
            ->leftJoin('tb_caller', 'tb_caller.que_trans_ids = tb_qtrans.que_trans_ids')
            ->orderBy(['tb_que.created_at' => SORT_ASC])
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'que_ids',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'que_ids',
                ],
                [
                    'attribute' => 'que_num',
                ],
                [
                    'attribute' => 'que_num_badge',
                    'value' => function($model, $key, $index, $column){
                        return \kartik\helpers\Html::badge($model['que_num'],['class' => 'badge badge-success','style' => 'width: 80px;font-size: 20px;font-weight: 600;']);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'service_id',
                ],
                [
                    'attribute' => 'service_group_id',
                ],
                [
                    'attribute' => 'que_status',
                ],
                [
                    'attribute' => 'service_name',
                ],
                [
                    'attribute' => 'service_group_name',
                ],
                [
                    'attribute' => 'que_status_name',
                ],
                [
                    'attribute' => 'que_trans_ids',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function($model, $key, $index, $column){
                        return Html::tag('code',Yii::$app->formatter->asDate($model['created_at'], 'php:H:i:s'));
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'service_prefix',
                ],
                [
                    'attribute' => 'created_time',
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{call} {recheck}',
                    'buttons' => [
                        'call' => function($url, $model, $key){
                            return Html::a(Icon::show('hand-pointer-o').'เรียกคิว',false,[
                                'class' => 'btn btn-primary btn-outline btn-lg activity-call',
                                'data-url' => $url,
                            ]);
                        },
                        'recheck' => function($url, $model, $key){
                            return Html::a(Icon::show('reply').'Recheck',false,[
                                'class' => 'btn btn-warning btn-outline btn-lg activity-recheck',
                                'data-url' => $url,
                            ]);
                        },
                    ],
                    
                    'urlCreator' => function ( $action, $model, $key, $index) {
                        if($action == 'call'){
                            return Url::to(['/app/calling/call-payment','que_ids' => $key]);
                        }
                        if($action == 'recheck'){
                            return Url::to(['/app/calling/recheck','que_ids' => $key,'que_trans_ids' => $model['que_trans_ids']]);
                        }
                    }
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #รายการคิวกำลังเรียก ชำระเงิน
    public static function getDataCallingPayment($bodyParams)
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_que.que_ids',
                'tb_que.que_num',
                'tb_que.service_id',
                'tb_que.service_group_id',
                'tb_que.que_status',
                'tb_que_status.que_status_name',
                'tb_service.service_name',
                'tb_service_group.service_group_name',
                'tb_que.created_at',
                'DATE_FORMAT(DATE_ADD(tb_que.created_at, INTERVAL 543 YEAR),\'%H:%i:%s\') AS created_time',
                'tb_qtrans.que_trans_ids',
                'tb_qtrans.que_trans_type',
                'tb_caller.caller_ids',
                'tb_caller.call_timestp',
                'tb_service_profile.service_profile_name',
                'tb_counter_service.counter_service_name',
                'tb_counter_service.counter_service_call_number',
                'tb_caller_status.caller_status_name',
                'tb_service.service_prefix'
            ])
            ->from('tb_que')
            ->where([
                'tb_caller.call_status' => [1,3],
                'tb_que.que_status' => 3,
                'tb_caller.counter_service_id' => $bodyParams['formData']['counter_service_id'],
                'tb_caller.service_profile_id' => $bodyParams['formData']['service_profile_id'],
                'tb_que.service_id' => $bodyParams['formData']['service_id'],
                'tb_qtrans.que_trans_status' => 0,
                'tb_qtrans.que_trans_type' => 1
            ])
            ->innerJoin('tb_que_status', 'tb_que_status.que_status_id = tb_que.que_status')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->innerJoin('tb_service_group', 'tb_service_group.service_group_id = tb_service.service_group_id')
            ->innerJoin('tb_qtrans', 'tb_qtrans.que_ids = tb_que.que_ids')
            ->innerJoin('tb_caller', 'tb_caller.que_trans_ids = tb_qtrans.que_trans_ids')
            ->innerJoin('tb_service_profile', 'tb_service_profile.service_profile_id = tb_caller.service_profile_id')
            ->innerJoin('tb_counter_service', 'tb_counter_service.counter_service_id = tb_caller.counter_service_id')
            ->innerJoin('tb_caller_status', 'tb_caller_status.caller_status_id = tb_caller.call_status')
            ->orderBy(['tb_caller.call_timestp' => SORT_ASC])
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'caller_ids',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'caller_ids',
                ],
                [
                    'attribute' => 'que_ids',
                ],
                [
                    'attribute' => 'que_trans_ids',
                ],
                [
                    'attribute' => 'service_profile_id',
                ],
                [
                    'attribute' => 'counter_service_id',
                ],
                [
                    'attribute' => 'call_timestp',
                ],
                [
                    'attribute' => 'que_num_badge',
                    'value' => function($model, $key, $index, $column){
                        return \kartik\helpers\Html::badge($model['que_num'],['class' => 'badge badge-primary','style' => 'width: 80px;font-size: 20px;font-weight: 600;']);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'call_status',
                ],
                [
                    'attribute' => 'caller_status_name',
                ],
                [
                    'attribute' => 'counter_service_name',
                ],
                [
                    'attribute' => 'counter_service_call_number',
                ],
                [
                    'attribute' => 'que_num',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function($model, $key, $index, $column){
                        return Html::tag('code',Yii::$app->formatter->asDate($model['created_at'], 'php:H:i:s'));
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_time',
                ],
                [
                    'attribute' => 'service_name',
                ],
                [
                    'attribute' => 'service_group_name',
                ],
                [
                    'attribute' => 'service_id',
                ],
                [
                    'attribute' => 'service_group_id',
                ],
                [
                    'attribute' => 'service_prefix',
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{recall} {hold} {end}',
                    'dropdown' => true,
                    'dropdownButton' => [
                        'class' => 'btn btn-primary btn-outline btn-lg',
                        'label' => 'ดำเนินการ'
                    ],
                    'dropdownOptions' => [
                        'class' => 'dropdown dropdown-action'
                    ],
                    'buttons' => [
                        'recall' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' เรียกคิว',false,[
                                'style' => 'color: inherit;font-size: 1.5em;',
                                'class' => 'activity-recall',
                                'data-url' => $url,
                            ]));
                        },
                        'hold' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' พักคิว',false,[
                                'style' => 'color: #f7a54a !important;font-size: 1.5em;',
                                'class' => 'activity-hold',
                                'data-url' => $url,
                            ]),[]);
                        },
                        'end' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' โอนคิว',false,[
                                'style' => 'color: #1ab394 !important;font-size: 1.5em;',
                                'class' => 'activity-end',
                                'data-url' => $url,
                            ]),[]);
                        },
                    ],
                    'urlCreator' => function ( $action, $model, $key, $index) {
                        if($action == 'recall'){
                            return Url::to(['/app/calling/recall-payment','caller_ids' => $key]);
                        }
                        if($action == 'hold'){
                            return Url::to(['/app/calling/hold-payment','caller_ids' => $key]);
                        }
                        if($action == 'end'){
                            return Url::to(['/app/calling/end-payment','caller_ids' => $key]);
                        }
                    }
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #รายการ พักคิว การเงิน
    public static function getDataHoldPayment($bodyParams)
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_que.que_ids',
                'tb_que.que_num',
                'tb_que.service_id',
                'tb_que.service_group_id',
                'tb_que.que_status',
                'tb_que_status.que_status_name',
                'tb_service.service_name',
                'tb_service_group.service_group_name',
                'tb_que.created_at',
                'DATE_FORMAT(DATE_ADD(tb_que.created_at, INTERVAL 543 YEAR),\'%H:%i:%s\') AS created_time',
                'tb_qtrans.que_trans_ids',
                'tb_qtrans.que_trans_type',
                'tb_caller.caller_ids',
                'tb_caller.call_timestp',
                'tb_service_profile.service_profile_name',
                'tb_counter_service.counter_service_name',
                'tb_counter_service.counter_service_call_number',
                'tb_caller_status.caller_status_name',
                'tb_service.service_prefix'
            ])
            ->from('tb_que')
            ->where([
                'tb_caller.call_status' => [2],
                'tb_que.que_status' => 3,
                //'tb_caller.counter_service_id' => $bodyParams['formData']['counter_service_id'],
                'tb_caller.service_profile_id' => $bodyParams['formData']['service_profile_id'],
                'tb_que.service_id' => $bodyParams['formData']['service_id'],
                'tb_qtrans.que_trans_status' => 0
            ])
            ->innerJoin('tb_que_status', 'tb_que_status.que_status_id = tb_que.que_status')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->innerJoin('tb_service_group', 'tb_service_group.service_group_id = tb_service.service_group_id')
            ->innerJoin('tb_qtrans', 'tb_qtrans.que_ids = tb_que.que_ids')
            ->innerJoin('tb_caller', 'tb_caller.que_trans_ids = tb_qtrans.que_trans_ids')
            ->innerJoin('tb_service_profile', 'tb_service_profile.service_profile_id = tb_caller.service_profile_id')
            ->innerJoin('tb_counter_service', 'tb_counter_service.counter_service_id = tb_caller.counter_service_id')
            ->innerJoin('tb_caller_status', 'tb_caller_status.caller_status_id = tb_caller.call_status')
            ->orderBy(['tb_caller.call_timestp' => SORT_ASC])
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'caller_ids',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'caller_ids',
                ],
                [
                    'attribute' => 'que_ids',
                ],
                [
                    'attribute' => 'que_trans_ids',
                ],
                [
                    'attribute' => 'service_profile_id',
                ],
                [
                    'attribute' => 'counter_service_id',
                ],
                [
                    'attribute' => 'call_timestp',
                ],
                [
                    'attribute' => 'que_num_badge',
                    'value' => function($model, $key, $index, $column){
                        return \kartik\helpers\Html::badge($model['que_num'],['class' => 'badge badge-warning','style' => 'width: 80px;font-size: 20px;font-weight: 600;']);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'call_status',
                ],
                [
                    'attribute' => 'caller_status_name',
                ],
                [
                    'attribute' => 'counter_service_name',
                ],
                [
                    'attribute' => 'counter_service_call_number',
                ],
                [
                    'attribute' => 'que_num',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function($model, $key, $index, $column){
                        return Html::tag('code',Yii::$app->formatter->asDate($model['created_at'], 'php:H:i:s'));
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_time',
                ],
                [
                    'attribute' => 'service_name',
                ],
                [
                    'attribute' => 'service_group_name',
                ],
                [
                    'attribute' => 'service_id',
                ],
                [
                    'attribute' => 'service_group_id',
                ],
                [
                    'attribute' => 'service_prefix',
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{recall} {end}',
                    'dropdown' => true,
                    'dropdownButton' => [
                        'class' => 'btn btn-primary btn-outline btn-lg',
                        'label' => 'ดำเนินการ'
                    ],
                    'dropdownOptions' => [
                        'class' => 'dropdown dropdown-action'
                    ],
                    'buttons' => [
                        'recall' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' เรียกคิว',false,[
                                'style' => 'color: inherit;font-size: 1.5em;',
                                'class' => 'activity-recall',
                                'data-url' => $url,
                            ]));
                        },
                        'end' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' โอนคิว',false,[
                                'style' => 'color: #1ab394 !important;font-size: 1.5em;',
                                'class' => 'activity-end',
                                'data-url' => $url,
                            ]),[]);
                        },
                    ],
                    'urlCreator' => function ( $action, $model, $key, $index) {
                        if($action == 'recall'){
                            return Url::to(['/app/calling/recall-payment','caller_ids' => $key]);
                        }
                        if($action == 'end'){
                            return Url::to(['/app/calling/end-payment','caller_ids' => $key]);
                        }
                    }
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #รายการคิวรอเรียก รับยา
    public static function getDataWaitingRecive($bodyParams)
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_que.que_ids',
                'tb_que.que_num',
                'tb_que.service_id',
                'tb_que.service_group_id',
                'tb_que.que_status',
                'tb_que_status.que_status_name',
                'tb_service.service_name',
                'tb_service_group.service_group_name',
                'tb_que.created_at',
                'DATE_FORMAT(DATE_ADD(tb_que.created_at, INTERVAL 543 YEAR),\'%H:%i:%s\') AS created_time',
                'tb_qtrans.que_trans_ids',
                'tb_qtrans.que_trans_type',
                'tb_caller.caller_ids',
                'tb_caller.call_timestp',
                'tb_service.service_prefix'
            ])
            ->from('tb_que')
            ->where([
                'tb_que.que_status' => 4,
                'tb_que.service_id' => $bodyParams['service_id'],
                'que_trans_status' => 0,
                'que_trans_type' => 2
            ])
            ->andWhere('tb_caller.caller_ids IS NULL')
            ->innerJoin('tb_que_status', 'tb_que_status.que_status_id = tb_que.que_status')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->innerJoin('tb_service_group', 'tb_service_group.service_group_id = tb_service.service_group_id')
            ->innerJoin('tb_qtrans', 'tb_qtrans.que_ids = tb_que.que_ids')
            ->leftJoin('tb_caller', 'tb_caller.que_trans_ids = tb_qtrans.que_trans_ids')
            ->orderBy(['tb_que.created_at' => SORT_ASC])
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'que_ids',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'que_ids',
                ],
                [
                    'attribute' => 'que_num',
                ],
                [
                    'attribute' => 'que_num_badge',
                    'value' => function($model, $key, $index, $column){
                        return \kartik\helpers\Html::badge($model['que_num'],['class' => 'badge badge-success','style' => 'width: 80px;font-size: 20px;font-weight: 600;']);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'service_id',
                ],
                [
                    'attribute' => 'service_group_id',
                ],
                [
                    'attribute' => 'que_status',
                ],
                [
                    'attribute' => 'service_name',
                ],
                [
                    'attribute' => 'service_group_name',
                ],
                [
                    'attribute' => 'que_status_name',
                ],
                [
                    'attribute' => 'que_trans_ids',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function($model, $key, $index, $column){
                        return Html::tag('code',Yii::$app->formatter->asDate($model['created_at'], 'php:H:i:s'));
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_time',
                ],
                [
                    'attribute' => 'service_prefix',
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{call} {recheck} {end}',
                    'buttons' => [
                        'call' => function($url, $model, $key){
                            return Html::a(Icon::show('hand-pointer-o').'เรียกคิว',false,[
                                'class' => 'btn btn-primary btn-outline btn-lg activity-call',
                                'data-url' => $url,
                            ]);
                        },
                        'recheck' => function($url, $model, $key){
                            return Html::a(Icon::show('reply').'Recheck',false,[
                                'class' => 'btn btn-warning btn-outline btn-lg activity-recheck',
                                'data-url' => $url,
                            ]);
                        },
                        'end' => function($url, $model, $key){
                            return Html::a(Icon::show('hand-pointer-o').'เสร็จสิ้น',false,[
                                'class' => 'btn btn-success btn-outline btn-lg activity-end',
                                'data-url' => $url,
                            ]);
                        },
                    ],
                    
                    'urlCreator' => function ( $action, $model, $key, $index) {
                        if($action == 'call'){
                            return Url::to(['/app/calling/call-recive','que_ids' => $key]);
                        }
                        if($action == 'recheck'){
                            return Url::to(['/app/calling/recheck','que_ids' => $key,'que_trans_ids' => $model['que_trans_ids']]);
                        }
                        if($action == 'end'){
                            return Url::to(['/app/calling/end-recive','que_ids' => $key]);
                        }
                    }
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #รายการคิวกำลังเรียก รับยา
    public static function getDataCallingRecive($bodyParams)
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_que.que_ids',
                'tb_que.que_num',
                'tb_que.service_id',
                'tb_que.service_group_id',
                'tb_que.que_status',
                'tb_que_status.que_status_name',
                'tb_service.service_name',
                'tb_service_group.service_group_name',
                'tb_que.created_at',
                'DATE_FORMAT(DATE_ADD(tb_que.created_at, INTERVAL 543 YEAR),\'%H:%i:%s\') AS created_time',
                'tb_qtrans.que_trans_ids',
                'tb_qtrans.que_trans_type',
                'tb_caller.caller_ids',
                'tb_caller.call_timestp',
                'tb_service_profile.service_profile_name',
                'tb_counter_service.counter_service_name',
                'tb_counter_service.counter_service_call_number',
                'tb_caller_status.caller_status_name',
                'tb_service.service_prefix'
            ])
            ->from('tb_que')
            ->where([
                'tb_caller.call_status' => [1,3],
                'tb_que.que_status' => 4,
                'tb_caller.counter_service_id' => $bodyParams['formData']['counter_service_id'],
                'tb_caller.service_profile_id' => $bodyParams['formData']['service_profile_id'],
                'tb_que.service_id' => $bodyParams['formData']['service_id'],
                'tb_qtrans.que_trans_status' => 0,
                'tb_qtrans.que_trans_type' => 2
            ])
            ->innerJoin('tb_que_status', 'tb_que_status.que_status_id = tb_que.que_status')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->innerJoin('tb_service_group', 'tb_service_group.service_group_id = tb_service.service_group_id')
            ->innerJoin('tb_qtrans', 'tb_qtrans.que_ids = tb_que.que_ids')
            ->innerJoin('tb_caller', 'tb_caller.que_trans_ids = tb_qtrans.que_trans_ids')
            ->innerJoin('tb_service_profile', 'tb_service_profile.service_profile_id = tb_caller.service_profile_id')
            ->innerJoin('tb_counter_service', 'tb_counter_service.counter_service_id = tb_caller.counter_service_id')
            ->innerJoin('tb_caller_status', 'tb_caller_status.caller_status_id = tb_caller.call_status')
            ->orderBy(['tb_caller.call_timestp' => SORT_ASC])
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'caller_ids',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'caller_ids',
                ],
                [
                    'attribute' => 'que_ids',
                ],
                [
                    'attribute' => 'que_trans_ids',
                ],
                [
                    'attribute' => 'service_profile_id',
                ],
                [
                    'attribute' => 'counter_service_id',
                ],
                [
                    'attribute' => 'call_timestp',
                ],
                [
                    'attribute' => 'que_num_badge',
                    'value' => function($model, $key, $index, $column){
                        return \kartik\helpers\Html::badge($model['que_num'],['class' => 'badge badge-primary','style' => 'width: 80px;font-size: 20px;font-weight: 600;']);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'call_status',
                ],
                [
                    'attribute' => 'caller_status_name',
                ],
                [
                    'attribute' => 'counter_service_name',
                ],
                [
                    'attribute' => 'counter_service_call_number',
                ],
                [
                    'attribute' => 'que_num',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function($model, $key, $index, $column){
                        return Html::tag('code',Yii::$app->formatter->asDate($model['created_at'], 'php:H:i:s'));
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_time',
                ],
                [
                    'attribute' => 'service_name',
                ],
                [
                    'attribute' => 'service_group_name',
                ],
                [
                    'attribute' => 'service_id',
                ],
                [
                    'attribute' => 'service_group_id',
                ],
                [
                    'attribute' => 'service_prefix',
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{recall} {hold} {end}',
                    'dropdown' => true,
                    'dropdownButton' => [
                        'class' => 'btn btn-primary btn-outline btn-lg',
                        'label' => 'ดำเนินการ'
                    ],
                    'dropdownOptions' => [
                        'class' => 'dropdown dropdown-action'
                    ],
                    'buttons' => [
                        'recall' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' เรียกคิว',false,[
                                'style' => 'color: inherit;font-size: 1.5em;',
                                'class' => 'activity-recall',
                                'data-url' => $url,
                            ]));
                        },
                        'hold' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' พักคิว',false,[
                                'style' => 'color: #f7a54a !important;font-size: 1.5em;',
                                'class' => 'activity-hold',
                                'data-url' => $url,
                            ]),[]);
                        },
                        'end' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' เสร็จสิ้น',false,[
                                'style' => 'color: #1ab394 !important;font-size: 1.5em;',
                                'class' => 'activity-end',
                                'data-url' => $url,
                            ]),[]);
                        },
                    ],
                    'urlCreator' => function ( $action, $model, $key, $index) {
                        if($action == 'recall'){
                            return Url::to(['/app/calling/recall-recive-drug','caller_ids' => $key]);
                        }
                        if($action == 'hold'){
                            return Url::to(['/app/calling/hold-recive-drug','caller_ids' => $key]);
                        }
                        if($action == 'end'){
                            return Url::to(['/app/calling/end-recive-drug','caller_ids' => $key]);
                        }
                    }
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #รายการ พักคิว รับยา
    public static function getDataHoldRecive($bodyParams)
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_que.que_ids',
                'tb_que.que_num',
                'tb_que.service_id',
                'tb_que.service_group_id',
                'tb_que.que_status',
                'tb_que_status.que_status_name',
                'tb_service.service_name',
                'tb_service_group.service_group_name',
                'tb_que.created_at',
                'DATE_FORMAT(DATE_ADD(tb_que.created_at, INTERVAL 543 YEAR),\'%H:%i:%s\') AS created_time',
                'tb_qtrans.que_trans_ids',
                'tb_qtrans.que_trans_type',
                'tb_caller.caller_ids',
                'tb_caller.call_timestp',
                'tb_service_profile.service_profile_name',
                'tb_counter_service.counter_service_name',
                'tb_counter_service.counter_service_call_number',
                'tb_caller_status.caller_status_name',
                'tb_service.service_prefix'
            ])
            ->from('tb_que')
            ->where([
                'tb_caller.call_status' => [2],
                'tb_que.que_status' => 4,
                //'tb_caller.counter_service_id' => $bodyParams['formData']['counter_service_id'],
                'tb_caller.service_profile_id' => $bodyParams['formData']['service_profile_id'],
                'tb_que.service_id' => $bodyParams['formData']['service_id'],
                'tb_qtrans.que_trans_status' => 0,
                'tb_qtrans.que_trans_type' => 2,
            ])
            ->innerJoin('tb_que_status', 'tb_que_status.que_status_id = tb_que.que_status')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->innerJoin('tb_service_group', 'tb_service_group.service_group_id = tb_service.service_group_id')
            ->innerJoin('tb_qtrans', 'tb_qtrans.que_ids = tb_que.que_ids')
            ->innerJoin('tb_caller', 'tb_caller.que_trans_ids = tb_qtrans.que_trans_ids')
            ->innerJoin('tb_service_profile', 'tb_service_profile.service_profile_id = tb_caller.service_profile_id')
            ->innerJoin('tb_counter_service', 'tb_counter_service.counter_service_id = tb_caller.counter_service_id')
            ->innerJoin('tb_caller_status', 'tb_caller_status.caller_status_id = tb_caller.call_status')
            ->orderBy(['tb_caller.call_timestp' => SORT_ASC])
            ->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
            'key' => 'caller_ids',
        ]);
        $columns = Yii::createObject([
            'class' => ColumnData::className(),
            'dataProvider' => $dataProvider,
            'formatter' => Yii::$app->formatter,
            'columns' => [
                [
                    'attribute' => 'caller_ids',
                ],
                [
                    'attribute' => 'que_ids',
                ],
                [
                    'attribute' => 'que_trans_ids',
                ],
                [
                    'attribute' => 'service_profile_id',
                ],
                [
                    'attribute' => 'counter_service_id',
                ],
                [
                    'attribute' => 'call_timestp',
                ],
                [
                    'attribute' => 'que_num_badge',
                    'value' => function($model, $key, $index, $column){
                        return \kartik\helpers\Html::badge($model['que_num'],['class' => 'badge badge-warning','style' => 'width: 80px;font-size: 20px;font-weight: 600;']);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'call_status',
                ],
                [
                    'attribute' => 'caller_status_name',
                ],
                [
                    'attribute' => 'counter_service_name',
                ],
                [
                    'attribute' => 'counter_service_call_number',
                ],
                [
                    'attribute' => 'que_num',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function($model, $key, $index, $column){
                        return Html::tag('code',Yii::$app->formatter->asDate($model['created_at'], 'php:H:i:s'));
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_time',
                ],
                [
                    'attribute' => 'service_name',
                ],
                [
                    'attribute' => 'service_group_name',
                ],
                [
                    'attribute' => 'service_id',
                ],
                [
                    'attribute' => 'service_group_id',
                ],
                [
                    'attribute' => 'service_prefix',
                ],
                [
                    'class' => ActionTable::className(),
                    'template' => '{recall} {end}',
                    'dropdown' => true,
                    'dropdownButton' => [
                        'class' => 'btn btn-primary btn-outline btn-lg',
                        'label' => 'ดำเนินการ'
                    ],
                    'dropdownOptions' => [
                        'class' => 'dropdown dropdown-action'
                    ],
                    'buttons' => [
                        'recall' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' เรียกคิว',false,[
                                'style' => 'color: inherit;font-size: 1.5em;',
                                'class' => 'activity-recall',
                                'data-url' => $url,
                            ]));
                        },
                        'end' => function($url, $model, $key){
                            return Html::tag('li',
                            Html::a(Icon::show('hand-pointer-o').' เสร็จสิ้น',false,[
                                'style' => 'color: #1ab394 !important;font-size: 1.5em;',
                                'class' => 'activity-end',
                                'data-url' => $url,
                            ]),[]);
                        },
                    ],
                    'urlCreator' => function ( $action, $model, $key, $index) {
                        if($action == 'recall'){
                            return Url::to(['/app/calling/recall-recive-drug','caller_ids' => $key]);
                        }
                        if($action == 'end'){
                            return Url::to(['/app/calling/end-recive-drug','caller_ids' => $key]);
                        }
                    }
                ],
            ],
        ]);
        return $columns->renderDataColumns();
    }

    #คิวที่กำลังเรียกล่าสุด
    public static function getDataQueCurrentCall($service_ids, $counter_service_ids)
    {
        $rows = (new \yii\db\Query())
            ->select([
                'tb_caller.caller_ids',
                'tb_caller.que_ids',
                'tb_caller.que_trans_ids',
                'tb_caller.service_profile_id',
                'tb_caller.counter_service_id',
                'tb_caller.call_timestp',
                'tb_caller.call_status',
                'tb_que.que_num',
                'tb_counter_service.counter_service_name',
                'tb_counter_service.counter_service_call_number',
                'tb_qtrans.que_trans_status',
                'tb_service.service_name',
                'tb_service.service_prefix',
            ])
            ->from('tb_caller')
            ->innerJoin('tb_que', 'tb_que.que_ids = tb_caller.que_ids')
            ->innerJoin('tb_counter_service', 'tb_counter_service.counter_service_id = tb_caller.counter_service_id')
            ->innerJoin('tb_qtrans', 'tb_qtrans.que_trans_ids = tb_caller.que_trans_ids')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->where([
                'tb_caller.call_status' => [1],
                'tb_que.service_id' => $service_ids,
                'tb_counter_service.counter_service_type_id' => $counter_service_ids,
                'que_trans_status' => 0,
            ])
            ->orderBy(['tb_caller.call_timestp' => SORT_ASC])
            ->one();
        return $rows;
    }

    #รายการคิวที่แสดงผลบนหน้าจอ display
    public static function getDataQueDisplay($service_ids,$counter_service_ids,$lastCall)
    {
        $rows = (new \yii\db\Query())
            ->select([
                'tb_caller.caller_ids',
                'tb_caller.que_ids',
                'tb_caller.que_trans_ids',
                'tb_caller.service_profile_id',
                'tb_caller.counter_service_id',
                'tb_caller.call_timestp',
                'tb_caller.call_status',
                'tb_que.que_num',
                'tb_counter_service.counter_service_name',
                'tb_counter_service.counter_service_call_number',
                'tb_qtrans.que_trans_status',
                'tb_service.service_name',
                'tb_service.service_prefix',
            ])
            ->from('tb_caller')
            ->innerJoin('tb_que', 'tb_que.que_ids = tb_caller.que_ids')
            ->innerJoin('tb_counter_service', 'tb_counter_service.counter_service_id = tb_caller.counter_service_id')
            ->innerJoin('tb_qtrans', 'tb_qtrans.que_trans_ids = tb_caller.que_trans_ids')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->where([
                'tb_caller.call_status' => [1, 3],
                'tb_que.service_id' => $service_ids,
                'tb_counter_service.counter_service_type_id' => $counter_service_ids,
                'que_trans_status' => 0,
            ])
            ->orderBy(['tb_caller.call_timestp' => SORT_DESC]);

        if ($lastCall) {
            $rows->andWhere('tb_caller.call_timestp <= :call_timestp', [':call_timestp' => $lastCall['call_timestp']]);
        }

        return $rows->all();
    }

    public static function getDataQueDisplayByCounterNumber($service_ids,$counter_service_ids,$lastCall,$counter_call_number,$config)
    {
        $query = (new \yii\db\Query())
            ->select([
                'tb_caller.caller_ids',
                'tb_caller.que_ids',
                'tb_caller.que_trans_ids',
                'tb_caller.service_profile_id',
                'tb_caller.counter_service_id',
                'tb_caller.call_timestp',
                'tb_caller.call_status',
                'tb_que.que_num',
                'tb_counter_service.counter_service_name',
                'tb_counter_service.counter_service_call_number',
                'tb_qtrans.que_trans_status',
                'tb_service.service_name',
                'tb_service.service_prefix',
            ])
            ->from('tb_caller')
            ->innerJoin('tb_que', 'tb_que.que_ids = tb_caller.que_ids')
            ->innerJoin('tb_counter_service', 'tb_counter_service.counter_service_id = tb_caller.counter_service_id')
            ->innerJoin('tb_qtrans', 'tb_qtrans.que_trans_ids = tb_caller.que_trans_ids')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->where([
                'tb_caller.call_status' => [1, 3],
                'tb_que.service_id' => $service_ids,
                'tb_counter_service.counter_service_type_id' => $counter_service_ids,
                'que_trans_status' => 0,
                'tb_counter_service.counter_service_call_number' => $counter_call_number,
            ])
            ->orderBy(['tb_caller.call_timestp' => SORT_DESC])
            ->limit($config['que_column_length']);

        if ($lastCall) {
            $query->andWhere('tb_caller.call_timestp <= :call_timestp', [':call_timestp' => $lastCall['call_timestp']]);
        }

        return $query->all();
    }

    public static function getDataQueHoldDisplay($service_ids, $counter_service_ids)
    {
        $rows = (new \yii\db\Query())
            ->select([
                'tb_caller.caller_ids',
                'tb_caller.que_ids',
                'tb_caller.que_trans_ids',
                'tb_caller.service_profile_id',
                'tb_caller.counter_service_id',
                'tb_caller.call_timestp',
                'tb_caller.call_status',
                'tb_que.que_num',
                'tb_counter_service.counter_service_name',
                'tb_counter_service.counter_service_call_number',
                'tb_qtrans.que_trans_status',
                'tb_service.service_name',
                'tb_service.service_prefix',
            ])
            ->from('tb_caller')
            ->innerJoin('tb_que', 'tb_que.que_ids = tb_caller.que_ids')
            ->innerJoin('tb_counter_service', 'tb_counter_service.counter_service_id = tb_caller.counter_service_id')
            ->innerJoin('tb_qtrans', 'tb_qtrans.que_trans_ids = tb_caller.que_trans_ids')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->where([
                'tb_caller.call_status' => [2],
                'tb_que.service_id' => $service_ids,
                'tb_counter_service.counter_service_type_id' => $counter_service_ids,
                'que_trans_status' => 0,
            ])
            ->orderBy(['tb_caller.call_timestp' => SORT_DESC])
            ->all();
        return $rows;
    }

    #รายการคิวรอยานาน จอแสดงผล
    public static function getDataQueWaitDisplay()
    {
        $rows = (new \yii\db\Query())
            ->select([
                'tb_que.que_ids',
                'tb_que.que_num',
                'tb_que.service_id',
                'tb_que.service_group_id',
                'tb_que.que_status',
                'tb_que_status.que_status_name',
                'tb_service.service_name',
                'tb_service_group.service_group_name',
                'tb_que.created_at',
                'DATE_FORMAT(DATE_ADD(tb_que.created_at, INTERVAL 543 YEAR),\'%H:%i:%s\') AS created_time'
            ])
            ->from('tb_que')
            ->innerJoin('tb_que_status', 'tb_que_status.que_status_id = tb_que.que_status')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->innerJoin('tb_service_group', 'tb_service_group.service_group_id = tb_service.service_group_id')
            ->where(['tb_que.que_status' => 2])
            ->orderBy(['tb_que.created_at' => SORT_ASC])
            ->all();
        return $rows;
    }

    public static function getDataQueDisplayByPrefix($service_ids, $counter_service_ids, $prefix, $lastCall)
    {
        $rows = (new \yii\db\Query())
            ->select([
                'tb_caller.caller_ids',
                'tb_caller.que_ids',
                'tb_caller.que_trans_ids',
                'tb_caller.service_profile_id',
                'tb_caller.counter_service_id',
                'tb_caller.call_timestp',
                'tb_caller.call_status',
                'tb_que.que_num',
                'tb_counter_service.counter_service_name',
                'tb_counter_service.counter_service_call_number',
                'tb_qtrans.que_trans_status',
                'tb_service.service_name',
                'tb_service.service_prefix',
            ])
            ->from('tb_caller')
            ->innerJoin('tb_que', 'tb_que.que_ids = tb_caller.que_ids')
            ->innerJoin('tb_counter_service', 'tb_counter_service.counter_service_id = tb_caller.counter_service_id')
            ->innerJoin('tb_qtrans', 'tb_qtrans.que_trans_ids = tb_caller.que_trans_ids')
            ->innerJoin('tb_service', 'tb_service.service_id = tb_que.service_id')
            ->where([
                'tb_caller.call_status' => [1, 3],
                'tb_que.service_id' => $service_ids,
                'tb_counter_service.counter_service_type_id' => $counter_service_ids,
                'que_trans_status' => 0,
                'tb_service.service_prefix' => $prefix,
            ])
            ->orderBy(['tb_caller.call_timestp' => SORT_DESC]);
            if ($lastCall) {
                $rows->andWhere('tb_caller.call_timestp <= :call_timestp', [':call_timestp' => $lastCall['call_timestp']]);
            }
        return $rows->all();
    }

    public static function getBadgeStatus($status)
    {
        if ($status == 0) {
            return \kartik\helpers\Html::badge(Icon::show('close') . ' ปิดใช้งาน', ['class' => 'badge badge-danger']);
        } elseif ($status == 1) {
            return \kartik\helpers\Html::badge(Icon::show('check') . ' เปิดใช้งาน', ['class' => 'badge badge-primary']);
        }
    }

    public static function getSoundname($id){
        $model = TbSound::findOne($id);
        if($model){
            return $model['sound_th'];
        }else{
            return '-';
        }
    }

    public static function getCounterServiceName($json){
        $li = [];
        if(!empty($json)){
            $arr = CoreUtility::string2Array($json);
            $model = TbCounterService::find()->where(['counter_service_id' => $arr])->all();
            foreach ($model as $key => $value) {
                $li[] = Html::tag('li',$value['counter_service_name']);
            }
        }
        return count($li) > 0 ? Html::tag('ul',implode("\n", $li)) : '';
    }

    public static function getServiceNames($json){
        $li = [];
        if(!empty($json)){
            $arr = CoreUtility::string2Array($json);
            $model = TbService::find()->where(['service_id' => $arr])->all();
            foreach ($model as $key => $value) {
                $li[] = Html::tag('li',$value['service_name']);
            }
        }
        return count($li) > 0 ? Html::tag('ul',implode("\n", $li)) : '';
    }

    public function getQueStatus($modelQue)
    {
        $service_name = '-';
        $countername = '';
        $class = 'badge';
        if($modelQue['que_status'] == 1){
            $service_name = 'รอตรวจสอบยา';
            $class = 'badge badge-success';
        }elseif($modelQue['que_status'] == 2){
            $service_name = 'รอยานาน';
            $class = 'badge badge-danger';
        }elseif($modelQue['que_status'] == 3){
            $modelQtran = TbQtrans::findOne(['que_trans_type' => 1, 'que_ids' => $modelQue['que_ids']]);
            $modelCaller = TbCaller::findOne(['que_ids' => $modelQue['que_ids'],'que_trans_ids' => $modelQtran['que_trans_ids']]);
            if($modelCaller){
                $counter = $modelCaller->counterService;
                $countername = $counter->counter_service_name;
                $service_name = 'ชำระเงิน';
            }else{
                $service_name = 'รอชำระเงิน';
            }
            $class = 'badge badge-default';
        }elseif($modelQue['que_status'] == 4){
            $modelQtran = TbQtrans::findOne(['que_trans_type' => 2, 'que_ids' => $modelQue['que_ids']]);
            $modelCaller = TbCaller::findOne(['que_ids' => $modelQue['que_ids'],'que_trans_ids' => $modelQtran['que_trans_ids']]);
            if($modelCaller){
                $counter = $modelCaller->counterService;
                $countername = $counter->counter_service_name;
                $service_name = 'รับยา';
            }else{
                $service_name = 'รอรับยา';
            }
            $class = 'badge badge-warning';
        }elseif($modelQue['que_status'] == 5){
            $service_name = 'เสร็จสิ้น!';
            $class = 'badge badge-primary';
        }
        return \kartik\helpers\Html::badge($service_name.' '.$countername, ['class' => $class,'style' => 'width:150px;']);;
    }
}
