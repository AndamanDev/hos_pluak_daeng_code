<?php

namespace frontend\modules\app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use frontend\modules\app\models\TbService;
use frontend\modules\app\models\TbServiceGroup;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use frontend\modules\app\models\TbQue;
use frontend\modules\app\models\TbQtrans;
use frontend\modules\app\models\TbCaller;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use frontend\modules\app\traits\ModelTrait;
use yii\web\HttpException;
use yii\web\Response;
use yii\helpers\Json;
use yii\helpers\Html;
use common\classes\AppQuery;

class KioskController extends \yii\web\Controller
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
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete-que' => ['post']
                ],
            ],
        ];
    }

    public function actionTicket()
    {
        $rows = [];
        $query = new \yii\db\Query;
        $servicegroup = TbServiceGroup::find()->all();
        foreach($servicegroup as $service){
            $services = $query->select([
                'tb_service.*',
            ])
            ->from('tb_service')
            ->where(['service_status' => TbService::STATUS_ACTIVE,'tb_service.service_group_id' => $service['service_group_id']])
            ->innerJoin('tb_service_group','tb_service_group.service_group_id = tb_service.service_group_id')->all();
            $rows[] = [
                'service_group_id' => $service['service_group_id'],
                'service_group_name' => $service['service_group_name'],
                'services' => $services
            ];
        }
        return $this->render('ticket',[
            'rows' => $rows
        ]);
    }

    public function actionCreateTicket($service_id,$service_group_id){
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $modelQue = new TbQue();
                $modelQue->setAttributes([
                    'service_id' => $service_id,
                    'service_group_id' => $service_group_id,
                    'que_status' => TbQue::STATUS_PRINT,
                ]);
                if($modelQue->save()){
                    $transaction->commit();
                    return [
                        'status' => 200,
                        'message' => 'successfully',
                        'modelQue' => $modelQue,
                        'url' => Url::to(['print-ticket','que_ids' => $modelQue->que_ids])
                    ];
                }else{
                    $transaction->rollBack();
                    throw new \yii\db\Exception("เกิดข้อผิดพลาด!");
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionPrintTicket($que_ids){
        $formatter = \Yii::$app->formatter;
        $modelQue = $this->findModelQue($que_ids);
        $modelService = $this->findModelService($modelQue['service_id']);
        $modelTicket = $this->findModelTicket($modelService['print_template_id']);
        $year = $formatter->asDate('now', 'php:Y') + 543;
        $count = 0;
        if($modelQue['que_status'] == 1){
            $sql = 'SELECT
            Count(tb_que.que_ids) as count
            FROM
            tb_que
            WHERE
            tb_que.service_group_id = :service_group_id AND
            tb_que.que_status = :que_status AND
            tb_que.que_ids < :que_ids';
            $params = [':service_group_id' => $modelQue['service_group_id'], ':que_ids' => $que_ids,':que_status' => $modelQue['que_status']];
            $count = Yii::$app->db->createCommand($sql)
                    ->bindValues($params)
                    ->queryScalar();
        } elseif ($modelQue['que_status'] == 2) {
            $sql = 'SELECT
            Count(tb_que.que_ids) as count
            FROM
            tb_que
            INNER JOIN tb_que_status ON tb_que_status.que_status_id = tb_que.que_status
            INNER JOIN tb_service ON tb_service.service_id = tb_que.service_id
            INNER JOIN tb_service_group ON tb_service_group.service_group_id = tb_service.service_group_id
            WHERE
            tb_que.que_status = :que_status AND
            tb_que.service_group_id = :service_group_id AND
            tb_que.created_at < :created_at
            ORDER BY
            tb_que.created_at ASC';
            $params = [':service_group_id' => $modelQue['service_group_id'], ':created_at' => $modelQue['created_at'],':que_status' => $modelQue['que_status']];
            $count = Yii::$app->db->createCommand($sql)
                    ->bindValues($params)
                    ->queryScalar();
        } elseif ($modelQue['que_status'] == 3) {
            $sql = 'SELECT
            Count(tb_que.que_ids) as count
            FROM
            tb_que
            INNER JOIN tb_que_status ON tb_que_status.que_status_id = tb_que.que_status
            INNER JOIN tb_service ON tb_service.service_id = tb_que.service_id
            INNER JOIN tb_service_group ON tb_service_group.service_group_id = tb_service.service_group_id
            INNER JOIN tb_qtrans ON tb_qtrans.que_ids = tb_que.que_ids
            LEFT JOIN tb_caller ON tb_caller.que_trans_ids = tb_qtrans.que_trans_ids
            WHERE
            tb_que.que_status = :que_status AND
            tb_caller.caller_ids IS NULL AND
            tb_qtrans.que_trans_type = 1 AND
            tb_que.service_group_id = :service_group_id AND
            tb_que.created_at < :created_at
            ORDER BY
            tb_que.created_at ASC            
            ';
            $params = [':service_group_id' => $modelQue['service_group_id'], ':created_at' => $modelQue['created_at'],':que_status' => $modelQue['que_status']];
            $count = Yii::$app->db->createCommand($sql)
                    ->bindValues($params)
                    ->queryScalar();
        } elseif ($modelQue['que_status'] == 4) {
            $sql = 'SELECT
            Count(tb_que.que_ids) as count
            FROM
            tb_que
            INNER JOIN tb_que_status ON tb_que_status.que_status_id = tb_que.que_status
            INNER JOIN tb_service ON tb_service.service_id = tb_que.service_id
            INNER JOIN tb_service_group ON tb_service_group.service_group_id = tb_service.service_group_id
            INNER JOIN tb_qtrans ON tb_qtrans.que_ids = tb_que.que_ids
            INNER JOIN tb_caller ON tb_caller.que_trans_ids = tb_qtrans.que_trans_ids
            INNER JOIN tb_service_profile ON tb_service_profile.service_profile_id = tb_caller.service_profile_id
            INNER JOIN tb_counter_service ON tb_counter_service.counter_service_id = tb_caller.counter_service_id
            INNER JOIN tb_caller_status ON tb_caller_status.caller_status_id = tb_caller.call_status
            WHERE
            tb_que.que_status = :que_status AND
            tb_qtrans.que_trans_status = 0 AND
            tb_caller.call_status IN (1, 3) AND
            tb_qtrans.que_trans_type = 2 AND
            tb_que.service_group_id = :service_group_id AND
            tb_que.created_at < :created_at
            ORDER BY
            tb_que.created_at ASC                        
            ';
            $params = [':service_group_id' => $modelQue['service_group_id'], ':created_at' => $modelQue['created_at'],':que_status' => $modelQue['que_status']];
            $count = Yii::$app->db->createCommand($sql)
                    ->bindValues($params)
                    ->queryScalar();
        }
        
        $template = strtr($modelTicket->template, [
            '{hos_name_th}' => $modelTicket->hos_name_th,
            '{q_hn}' => $modelQue->que_hn,
            '{pt_name}' => $modelQue->pt_name,
            '{q_num}' => $modelQue->que_num,
            '{pt_visit_type}' => '',
            '{sec_name}' => '',
            '{time}' => $formatter->asDate('now', 'php:d M '.substr($year, 2)).' '.$formatter->asDate('now','php:H:i').' น.',
            '{user_print}' => Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->profile->name,
            '{qwaiting}' => $count,
            '/images/logo/pmh-logo.png' => $modelTicket->logo_path ? $modelTicket->logo_base_url.'/'.$modelTicket->logo_path : '/images/logo/logoBBH.png'
        ]);
        return $this->renderAjax('print-ticket',[
            'modelQue' => $modelQue,
            'modelTicket' => $modelTicket,
            'template' => $template,
            'modelService' => $modelService,
        ]);
    }

    /* public function actionDataQue()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $response = Yii::$app->api->callApiData('GET','que/data-que-list');
            if ($response->isOk) {
                return ['data' => $response->data['data']];
            } else {
                if(isset($response->data['message'])){
                    throw new HttpException($response->data['status'], $response->data['message']);
                }else{
                    throw new HttpException($response->data['status'], $response->data['data']['message']);
                }
            }
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    } */

    public function actionDataQue()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $data = AppQuery::getDataQueList();
            return ['data' => $data];
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionDeleteQue($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModelQue($id);
        $model->delete();
        TbCaller::deleteAll(['que_ids' => $id]);
        TbQtrans::deleteAll(['que_ids' => $id]);

        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true];
        }else{
            return $this->redirect(['ticket']);
        }
    }

    public function actionUpdateQue($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModelQue($id);
        $oldservice = $model['service_id'];

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "แก้ไขรายการคิว #".$model['que_num'],
                    'content'=>$this->renderAjax('_form_que', [
                        'model' => $model,
                    ]),
                    'footer'=> '',
                ];
            }else if($model->load($request->post())){
                $data = $request->post('TbQue',[]);
                if($oldservice != $data['service_id']){
                    $model->que_num = $model->generateQnumber();
                }
                if($model->save()){
                    return [
                        'title'=> "แก้ไขรายการคิว #".$model['que_num'],
                        'content'=>'<span class="text-success">บันทึกสำเร็จ!</span>',
                        'footer'=> Html::button('Close',['class'=>'btn btn-default','data-dismiss'=>"modal"]),
                        'status' => '200',
                    ];
                }else{
                    return [
                        'title'=> "แก้ไขรายการคิว #".$model['que_num'],
                        'content'=>$this->renderAjax('_form_que', [
                            'model' => $model,
                        ]),
                        'footer'=> '',
                        'status' => 'validate',
                        'validate' => ActiveForm::validate($model),
                    ];
                }
            }else{
                return [
                    'title'=> "แก้ไขรายการคิว #".$model['que_num'],
                    'content'=>$this->renderAjax('_form_que', [
                        'model' => $model,
                    ]),
                    'footer'=> '',
                    'status' => 'validate',
                    'validate' => ActiveForm::validate($model),
                ];
            }
        }else{
            throw new MethodNotAllowedHttpException('method not allowed.'); 
        }
    }

    public function actionChildServiceGroup() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $id = end($_POST['depdrop_parents']);
            $list = TbService::find()->andWhere(['service_group_id'=>$id])->asArray()->all();
            $selected  = null;
            if ($id != null && count($list) > 0) {
                $selected = '';
                foreach ($list as $i => $data) {
                    $out[] = ['id' => $data['service_id'], 'name' => $data['service_name']];
                    if ($i == 0) {
                        $selected = $data['service_id'];
                    }
                }
                // Shows how you can preselect a value
                echo Json::encode(['output' => $out, 'selected'=>$selected]);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected'=>'']);
    }

}
