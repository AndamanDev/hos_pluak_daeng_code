<?php

namespace frontend\modules\app\controllers;

use frontend\modules\app\models\TbCounterService;
use frontend\modules\app\models\TbDisplay;
use frontend\modules\app\models\TbService;
use frontend\modules\app\traits\ModelTrait;
use inspinia\utils\CoreUtility;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use common\classes\AppQuery;
use yii\web\BadRequestHttpException;

class DisplayController extends \yii\web\Controller
{
    use ModelTrait;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'view', 'data-que-wait', 'data-display', 'data-hold'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [

                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $query = TbDisplay::find()->where(['display_status' => 1])->all();
        return $this->render('index', [
            'query' => $query,
        ]);
    }

    public function actionView($id)
    {
        $this->layout = '@frontend/views/layouts/display.php';
        $counters = [];
        $config = $this->findModelDisplay($id);
        $config->display_css = strip_tags($config['display_css']);
        $service_ids = CoreUtility::string2Array($config['service_id']);
        $counter_service_ids = CoreUtility::string2Array($config['counter_service_id']);
        $modelCounter = TbCounterService::find()->where(['counter_service_type_id' => $counter_service_ids])->all();
        foreach ($modelCounter as $item) {
            $counters[] = (string) $item['counter_service_id'];
        }
        return $this->render('view', [
            'config' => $config,
            'service_ids' => $service_ids,
            'counters' => $counters,
        ]);
    }

    public function actionDataDisplay()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $config = $request->post('config', []);
            $items = [];

            $service_ids = CoreUtility::string2Array($config['service_id']);
            $counter_service_ids = CoreUtility::string2Array($config['counter_service_id']);

            /* $modelService = TbService::find()->where(['service_id' => $service_ids, 'service_status' => 1])->all();
            $prefixs = ArrayHelper::map($modelService, 'service_id', 'service_prefix'); */

            $lastCall = AppQuery::getDataQueCurrentCall($service_ids, $counter_service_ids);//คิวที่กำลังเรียก

            $rows = AppQuery::getDataQueDisplay($service_ids,$counter_service_ids,$lastCall);
            
            if(count($rows) > 0 && $config['que_column_length'] > 1){
                //เรียงข้อมูลใหม่ เรียงตามหมายเลข ช่องบริการ
                ArrayHelper::multisort($rows, ['counter_service_call_number', 'counter_service_call_number'], [SORT_ASC, SORT_ASC]);
                //group ข้อมูลเคาท์เตอร์
                $counter_call_number = ArrayHelper::map($rows,'counter_service_call_number','counter_service_call_number');
                foreach($counter_call_number as $number){

                    $query = AppQuery::getDataQueDisplayByCounterNumber($service_ids,$counter_service_ids,$lastCall,$number,$config);

                    $tempArr = [];
                    $data = [];
                    $que_nums = ArrayHelper::getColumn($query,'que_num');
                    foreach($que_nums as $qnum){
                        $tempArr[] = Html::tag('span', $qnum, ['class' => $qnum]);
                        $data[] = $qnum;
                    }
                    /* if(count($que_nums) < $config['que_column_length']){
                        for ($x = count($que_nums); $x < $config['que_column_length']; $x++) {
                            $tempArr[] = '-';
                        }
                    } */
                    $items[] = [
                        'que_number' => implode(" | ",$tempArr),
                        'counter_number' => Html::tag('span', $number, ['class' => $number]),
                        'data' => $data,
                    ];
                }

                #ถ้าไม่มีข้อมูลคิว
                if (count($items) < $config['page_length']) {
                    $items = ArrayHelper::merge($items, $this->renderDefaultData($config, count($items)));
                }
            }else{
                $items = $this->renderItems($rows);
                #ถ้าไม่มีข้อมูลคิว
                if (count($rows) < $config['page_length']) {
                    $items = ArrayHelper::merge($items, $this->renderDefaultData($config));
                }
            }

            return [
                'data' => $items,
            ];
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionDataHold()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $config = $request->post('config', []);
            $service_ids = CoreUtility::string2Array($config['service_id']);
            $counter_service_ids = CoreUtility::string2Array($config['counter_service_id']);
            $items = [];
            $rows = AppQuery::getDataQueHoldDisplay($service_ids, $counter_service_ids);
            if(count($rows) == 0){
                $items[] = [
                    'text' => '<div class="ribbon ribbon-right ribbon-shadow ribbon-border-dash ribbon-round ribbon-hold uppercase" style="width:100%;padding: 0.2em 1em;">
                    '.$config['text_hold'].'
                    </div>',
                    'que_number' => '-',
                    'data' => []
                ];
            }else{
                $que_numbers = ArrayHelper::getColumn($rows,'que_num');
                $items[] = [
                    'text' => '<div class="ribbon ribbon-right ribbon-shadow ribbon-border-dash ribbon-round ribbon-hold uppercase" style="width:100%;padding: 0.2em 1em;">
                    '.$config['text_hold'].'
                    </div>',
                    'que_number' => count($que_numbers) > 0 ? Html::tag('marquee',implode(" | ",$que_numbers),['direction' => 'left']) : '-',
                    'data' => $que_numbers
                ];
            }
            return [
                'data' => $items
            ];
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionDataQueWait()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $config = $request->post('config', []);
            $service_ids = CoreUtility::string2Array($config['service_id']);
            $counter_service_ids = CoreUtility::string2Array($config['counter_service_id']);
            $items = [];
            $rows = AppQuery::getDataQueWaitDisplay();
            if(count($rows) == 0){
                $items[] = [
                    'text' => '<div class="ribbon ribbon-right ribbon-shadow ribbon-border-dash ribbon-round ribbon-wait uppercase" style="width:100%;padding: 0.2em 1em;">
                    คิวกำลังจัดยา
                    </div>',
                    'que_number' => '-',
                    'data' => []
                ];
            }else{
                $que_numbers = ArrayHelper::getColumn($rows,'que_num');
                $items[] = [
                    'text' => '<div class="ribbon ribbon-right ribbon-shadow ribbon-border-dash ribbon-round ribbon-wait uppercase" style="width:100%;padding: 0.2em 1em;">
                    คิวกำลังจัดยา
                    </div>',
                    'que_number' => count($que_numbers) > 0 ? Html::tag('marquee',implode(" | ",$que_numbers),['direction' => 'left']) : '-',
                    'data' => $que_numbers
                ];
            }
            return [
                'data' => $items
            ];
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    protected function renderDefaultData($config, $x = 0)
    {
        $items = [];
        for ($x; $x < $config['page_length']; $x++) {
            $arr = [
                'que_number' => '-',
                'counter_number' => '-',
                'data' => []
            ];
            $items[] = $arr;
        }
        return $items;
    }

    protected function renderItems($rows)
    {
        $items = [];
        foreach ($rows as $row) {
            $arr = [
                'que_number' => Html::tag('span', $row['que_num'], ['class' => $row['que_num']]),
                'counter_number' => Html::tag('span', $row['counter_service_call_number'], ['class' => $row['que_num']]),
                'data' => [$row['que_num']],
                'counter_service_call_number' => $row['counter_service_call_number']
            ];
            $items[] = $arr;
        }
        return $items;
    }
}
