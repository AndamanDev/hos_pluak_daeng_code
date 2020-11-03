<?php
namespace backend\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;
use inspinia\widgets\tablecolumn\ActionTable;
use inspinia\widgets\tablecolumn\ColumnTable;
use inspinia\widgets\tablecolumn\ColumnData;
use yii\data\ArrayDataProvider;
use kartik\icons\Icon;
use yii\helpers\Url;
use yii\helpers\Html;
use common\classes\AppQuery;
use frontend\modules\app\models\TbServiceProfile;
use frontend\modules\app\models\TbService;
use inspinia\utils\CoreUtility;
use frontend\modules\app\models\TbQtrans;
use frontend\modules\app\traits\ModelTrait;
use frontend\modules\app\models\TbCounterService;
use frontend\modules\app\components\SoundComponent;
use frontend\modules\app\models\TbCaller;
use frontend\modules\app\models\TbQue;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

class QueController extends ActiveController
{
    use ModelTrait;

    public $modelClass = 'frontend\modules\app\models\TbQue';

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create'], $actions['update'], $actions['delete'], $actions['view']);

        return $actions;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBearerAuth::className(),
            ],
        ];
        $behaviors['verbs'] = [
            'class' => \yii\filters\VerbFilter::className(),
            'actions' => [
                'index' => ['get'],
                'view' => ['get'],
                'create' => ['post'],
                'update' => ['put'],
                'delete' => ['delete'],
                'data-que-list' => ['get'],
                'data-wait-checkdrug' => ['post'],
                'data-wait-drug-checkdrug' => ['post'],
                'data-wait-payment-checkdrug' => ['post'],
                'data-waiting-payment' => ['post'],
                'data-calling-payment' => ['post'],
                'data-hold-payment' => ['post'],
                'data-waiting-recive' => ['post'],
                'data-calling-recive' => ['post'],
                'data-form-service-profile' => ['get'],
                'update-status-checkdrug' => ['post'],
                'delete-que' => ['post'],
                'call-payment' => ['post'],
                'hold-payment' => ['post'],
                'end-payment' => ['post'],
                'call-recive-selected' => ['post'],
                'call-recive' => ['post'],
                'recall-recive-drug' => ['post'],
                'hold-recive-drug' => ['post'],
                'end-recive-drug' => ['post'],
                'dashboard-data' => ['get'],
                'que-scanner' => ['get'],
                'data-que' => ['get'],
                'loadmore-que-data' => ['get']
            ],
        ];
        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);
        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
        ];
        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = [
            'options',
            'dashboard-data',
            'que-scanner'
        ];
        // setup access
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['index', 'view', 'create', 'update', 'delete'], //only be applied to
            'rules' => [
                [
                    'allow' => true,
                    'actions' => [
                        'index', 'view', 'create', 'update', 'delete', 'data-que-list',
                        'data-wait-checkdrug','data-wait-drug-checkdrug',
                        'data-wait-payment-checkdrug', 'data-waiting-payment',
                        'data-calling-payment','data-hold-payment',
                        'data-waiting-recive','data-calling-recive',
                        'data-form-service-profile','update-status-checkdrug', 'delete-que',
                        'call-payment','recall-payment','hold-payment','end-payment',
                        'call-recive-selected','call-recive','recall-recive-drug',
                        'hold-recive-drug','end-recive-drug','dashboard-data',
                        'que-scanner','data-que', 'loadmore-que-data'
                    ],
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actionDataQueList()
    {
        $data = AppQuery::getDataQueList();
        return $data;
    }

    public function actionDataQue()
    {
        $data = AppQuery::getDataQue();
        return $data;
    }

    public function actionLoadmoreQueData($qids)
    {
        $data = AppQuery::LoadmoreQueData($qids);
        return $data;
    }

    #รายการคิวตรวจสอบยา
    public function actionDataWaitCheckdrug()
    {
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        return AppQuery::getDataWaitCheckdrug($bodyParams);
    }

    #คิวรอยานาน ตรวจสอบยา
    public function actionDataWaitDrugCheckdrug()
    {
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        return AppQuery::getDataWaitDrugCheckdrug($bodyParams);
    }

    #คิวรอชำระเงิน ตรวจสอบยา
    public function actionDataWaitPaymentCheckdrug()
    {
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        return AppQuery::getDataWaitPaymentCheckdrug($bodyParams);
    }

    #รายการคิวรอเรียก ชำระเงิน
    public function actionDataWaitingPayment()
    {
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        return AppQuery::getDataWaitingPayment($bodyParams);
    }

    #รายการคิวกำลังเรียก ชำระเงิน
    public function actionDataCallingPayment()
    {
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        return AppQuery::getDataCallingPayment($bodyParams);
    }

    #รายการ พักคิว การเงิน
    public function actionDataHoldPayment()
    {
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        return AppQuery::getDataHoldPayment($bodyParams);
    }

    #รายการคิวรอเรียก รับยา
    public function actionDataWaitingRecive()
    {
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        return AppQuery::getDataWaitingRecive($bodyParams);
    }

    #รายการคิวกำลังเรียก รับยา
    public function actionDataCallingRecive()
    {
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        return AppQuery::getDataCallingRecive($bodyParams);
    }

    #รายการ พักคิว รับยา
    public function actionDataHoldRecive()
    {
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        return AppQuery::getDataHoldRecive($bodyParams);
    }

    public function actionDataFormServiceProfile(){
        $query = TbServiceProfile::find()->where(['service_profile_status' => 1])->all();
        $items = [];
        foreach($query as $item){
            $dataCounters = TbCounterService::find()->where(['counter_service_type_id' => $item['counter_service_type_id']])->asArray()->all();
            $counters = [];
            foreach($dataCounters as $counter){
                $counters[] = [
                    'counter_service_id' => (int)$counter['counter_service_id'],
                    'counter_service_name' => $counter['counter_service_name'],
                ];
            }
            $items[] = [
                'service_profile_id' => $item['service_profile_id'],
                'service_profile_name' => $item['service_profile_name'],
                'counter_service_type_id' => $item['counter_service_type_id'],
                'service_id' => CoreUtility::string2Array($item['service_id']),
                'services' => TbService::find()->where(['service_id' => CoreUtility::string2Array($item['service_id']), 'service_status' => 1])->asArray()->all(),
                'counters' => $counters,
            ];
        }
        return $items;
    }

    public function actionUpdateStatusCheckdrug()
    {
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        $data = $bodyParams['data'];
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $modelQue = $this->findModelQue($data['que_ids']);
            $modelProfile = $this->findModelServiceProfile($bodyParams['service_profile_id']);
            $modelProfile->service_id = CoreUtility::string2Array($modelProfile['service_id']);

            if ($bodyParams['type'] == 'payment') { #ชำระเงิน
                $que_trans_type = TbQtrans::TYPE_PAYMENT;
                $que_status = 3;
            }
            if ($bodyParams['type'] == 'not-payment') { #ไม่ชำระเงิน
                $que_trans_type = TbQtrans::TYPE_RECIVE_DRUG;
                $que_status = 4;
            }
            if ($bodyParams['type'] == 'waiting-drug') { #รอยานาน
                $que_trans_type = TbQtrans::TYPE_WAITING_DRUG;
                $que_status = 2;
            }

            if(TbQtrans::TYPE_WAITING_DRUG == $que_trans_type){
                $modelQue->setAttributes([
                    'que_status' => $que_status,
                ]);
                if ($modelQue->save()) {
                    $transaction->commit();
                    $response = \Yii::$app->getResponse();
                    $response->setStatusCode(200);
                    return [
                        'data' => $data,
                        'modelQue' => $modelQue,
                        'modelProfile' => $modelProfile,
                    ];
                } else {
                    $transaction->rollBack();
                    throw new HttpException(422, Json::encode($modelQue->errors));
                }
            }else{
                $modelQtrans = new TbQtrans();
                $modelQtrans->setAttributes([
                    'que_trans_type' => $que_trans_type,
                    'que_ids' => $data['que_ids'],
                    'que_trans_status' => TbQtrans::STATUS_UNACTIVE,
                ]);
                $modelQue->setAttributes([
                    'que_status' => $que_status,
                ]);
                if ($modelQue->save() && $modelQtrans->save()) {
                    $response = \Yii::$app->getResponse();
                    $response->setStatusCode(200);
                    $transaction->commit();
                    return [
                        'data' => $data,
                        'modelQue' => $modelQue,
                        'modelQtrans' => $modelQtrans,
                        'modelProfile' => $modelProfile,
                    ];
                } else {
                    $transaction->rollBack();
                    throw new HttpException(422, Json::encode([$modelQue->errors,$modelQtrans->errors]));
                }
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function actionDeleteQue()
    {
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        $model = $this->findModelQue($bodyParams['que_ids']);
        $model->delete();
        TbQtrans::deleteAll(['que_ids' => $bodyParams['que_ids']]);

        return 'Deleted!';
    }

    //เรียกคิวการเงิน
    public function actionCallPayment()
    {
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        $data = $bodyParams['data'];
        $formData = $bodyParams['formData'];
        $modelQue = $this->findModelQue($data['que_ids']);
        $modelProfile = $this->findModelServiceProfile($formData['service_profile_id']);
        $modelProfile->service_id = CoreUtility::string2Array($modelProfile['service_id']);
        
        $modelCaller = new TbCaller();
        $modelCaller->setAttributes([
            'que_ids' => $data['que_ids'],
            'que_trans_ids' => $data['que_trans_ids'],
            'service_profile_id' => $formData['service_profile_id'],
            'counter_service_id' => $formData['counter_service_id'],
            'call_timestp' => Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s'),
            'call_status' => TbCaller::STATUS_CALLING #เรียกคิว
        ]);
        if ($modelCaller->save()) {
            $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
            $modelCounterService = $this->findModelCounterService($formData['counter_service_id']);
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(200);
            return [
                'data' => $data,
                'modelQue' => $modelQue,
                'modelProfile' => $modelProfile,
                'formData' => $formData,
                'modelCaller' => $modelCaller,
                'modelQtrans' => $modelQtrans,
                'modelCounterService' => $modelCounterService,
                'media_files' => $this->getMediaFiles($modelQue['que_num'],$formData['counter_service_id']),
            ];
        } else {
            throw new HttpException(422, Json::encode($modelCaller->errors));
        }
    }

    public function actionRecallPayment(){
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        $data = $bodyParams['data'];
        $formData = $bodyParams['formData'];
        $modelProfile = $this->findModelServiceProfile($formData['service_profile_id']);
        $modelProfile->service_id = CoreUtility::string2Array($modelProfile['service_id']);
        $modelQue = $this->findModelQue($data['que_ids']);
        $modelCaller = $this->findModelCaller($data['caller_ids']);
        $modelCaller->setAttributes([
            'call_timestp' => Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s'),
            'call_status' => TbCaller::STATUS_CALLING,
        ]);
        $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
        $modelCounterService = $this->findModelCounterService($formData['counter_service_id']);
        if($modelCaller->save()){
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(200);
            return [
                'data' => $data,
                'modelQue' => $modelQue,
                'modelProfile' => $modelProfile,
                'formData' => $formData,
                'modelCaller' => $modelCaller,
                'modelQtrans' => $modelQtrans,
                'modelCounterService' => $modelCounterService,
                'media_files' => $this->getMediaFiles($modelQue['que_num'],$formData['counter_service_id']),
            ];
        }else {
            throw new HttpException(422, Json::encode($modelCaller->errors));
        }
    }

    public function actionHoldPayment(){
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        $data = $bodyParams['data'];
        $formData = $bodyParams['formData'];
        $modelProfile = $this->findModelServiceProfile($formData['service_profile_id']);
        $modelProfile->service_id = CoreUtility::string2Array($modelProfile['service_id']);
        $modelQue = $this->findModelQue($data['que_ids']);
        $modelCaller = $this->findModelCaller($data['caller_ids']);
        $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
        $modelCaller->setAttributes([
            'call_status' => TbCaller::STATUS_HOLD,
        ]);
        if($modelCaller->save()){
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(200);
            return [
                'data' => $data,
                'modelQue' => $modelQue,
                'modelProfile' => $modelProfile,
                'formData' => $formData,
                'modelCaller' => $modelCaller,
                'modelQtrans' => $modelQtrans,
            ];
        }else {
            throw new HttpException(422, Json::encode($modelCaller->errors));
        }
    }

    public function actionEndPayment(){
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        $data = $bodyParams['data'];
        $formData = $bodyParams['formData'];
        $modelProfile = $this->findModelServiceProfile($formData['service_profile_id']);
        $modelProfile->service_id = CoreUtility::string2Array($modelProfile['service_id']);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $modelQue = $this->findModelQue($data['que_ids']);
            $modelQue->setAttributes([
                'que_status' => 4, //รอรับยา
                'payment_at' => Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s')
            ]);
            $modelCaller = $this->findModelCaller($data['caller_ids']);
            $modelQtransOld = $this->findModelQtrans($modelCaller['que_trans_ids']);
            $modelQtransOld->que_trans_status = TbQtrans::STATUS_ACTIVE;

            $modelQtrans = new TbQtrans();
            $modelQtrans->setAttributes([
                'que_trans_type' => TbQtrans::TYPE_RECIVE_DRUG,
                'que_ids' => $modelQue['que_ids'],
                'que_trans_status' => TbQtrans::STATUS_UNACTIVE,
            ]);
            if($modelQue->save() && $modelQtrans->save() && $modelQtransOld->save()){
                $transaction->commit();
                $response = \Yii::$app->getResponse();
                $response->setStatusCode(200);
                return [
                    'data' => $data,
                    'modelQue' => $modelQue,
                    'modelProfile' => $modelProfile,
                    'formData' => $formData,
                    'modelCaller' => $modelCaller,
                ];
            }else {
                $transaction->rollBack();
                throw new HttpException(422, Json::encode([
                    'modelCaller' => $modelCaller->errors,
                    'modelQtrans' => $modelQtrans->errors,
                    'modelQue' => $modelQue->errors
                ]));
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function actionCallReciveSelected(){
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        $selectedData = $bodyParams['selectedData'];
        $formData = $bodyParams['formData'];
        $modelProfile = $this->findModelServiceProfile($formData['service_profile_id']);
        $modelProfile->service_id = CoreUtility::string2Array($modelProfile['service_id']);
        $transaction = Yii::$app->db->beginTransaction();
        $responseData = [];
        $success = false;
        try {
            foreach($selectedData as $key => $item){
                $modelCaller = new TbCaller();
                $modelCaller->setAttributes([
                    'que_ids' => $item['que_ids'],
                    'que_trans_ids' => $item['que_trans_ids'],
                    'service_profile_id' => $formData['service_profile_id'],
                    'counter_service_id' => $formData['counter_service_id'],
                    'call_timestp' => Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s'),
                    'call_status' => TbCaller::STATUS_CALLING #เรียกคิว
                ]);
                if ($modelCaller->save()) {
                    $modelQue = $this->findModelQue($item['que_ids']);
                    $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
                    $modelCounterService = $this->findModelCounterService($formData['counter_service_id']);
                    $responseData[] = [
                        'data' => $item,
                        'modelQue' => $modelQue,
                        'modelProfile' => $modelProfile,
                        'formData' => $formData,
                        'modelCaller' => $modelCaller,
                        'modelQtrans' => $modelQtrans,
                        'modelCounterService' => $modelCounterService,
                        'media_files' => $this->getMediaFiles($modelQue['que_num'],$formData['counter_service_id']),
                    ];
                    $success = true;
                }else {
                    throw new HttpException(422, Json::encode($modelCaller->errors));
                }
            }
            if($success){
                $transaction->commit();
                $response = \Yii::$app->getResponse();
                $response->setStatusCode(200);
                return $responseData;
            }else{
                $transaction->rollBack();
                throw new HttpException(422, "เกิดข้อผิดพลาด!");
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function actionCallRecive(){
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        $data = $bodyParams['data'];
        $formData = $bodyParams['formData'];
        $modelProfile = $this->findModelServiceProfile($formData['service_profile_id']);
        $modelProfile->service_id = CoreUtility::string2Array($modelProfile['service_id']);
        $modelQue = $this->findModelQue($data['que_ids']);
        $modelCaller = new TbCaller();
        $modelCaller->setAttributes([
            'que_ids' => $data['que_ids'],
            'que_trans_ids' => $data['que_trans_ids'],
            'service_profile_id' => $formData['service_profile_id'],
            'counter_service_id' => $formData['counter_service_id'],
            'call_timestp' => Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s'),
            'call_status' => TbCaller::STATUS_CALLING #เรียกคิว
        ]);
        if ($modelCaller->save()) {
            $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
            $modelCounterService = $this->findModelCounterService($formData['counter_service_id']);
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(200);
            return [
                'data' => $data,
                'modelQue' => $modelQue,
                'modelProfile' => $modelProfile,
                'formData' => $formData,
                'modelCaller' => $modelCaller,
                'modelQtrans' => $modelQtrans,
                'modelCounterService' => $modelCounterService,
                'media_files' => $this->getMediaFiles($modelQue['que_num'],$formData['counter_service_id']),
            ];
        }else{
            throw new HttpException(422, Json::encode($modelCaller->errors));
        }
    }

    public function actionRecallReciveDrug(){
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        $data = $bodyParams['data'];
        $formData = $bodyParams['formData'];
        $modelProfile = $this->findModelServiceProfile($formData['service_profile_id']);
        $modelProfile->service_id = CoreUtility::string2Array($modelProfile['service_id']);
        $modelQue = $this->findModelQue($data['que_ids']);
        $modelCaller = $this->findModelCaller($data['caller_ids']);
        $modelCaller->setAttributes([
            'call_timestp' => Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s'),
            'call_status' => TbCaller::STATUS_CALLING,
        ]);
        $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
        $modelCounterService = $this->findModelCounterService($formData['counter_service_id']);
        if($modelCaller->save()){
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(200);
            return [
                'data' => $data,
                'modelQue' => $modelQue,
                'modelProfile' => $modelProfile,
                'formData' => $formData,
                'modelCaller' => $modelCaller,
                'modelQtrans' => $modelQtrans,
                'modelCounterService' => $modelCounterService,
                'media_files' => $this->getMediaFiles($modelQue['que_num'],$formData['counter_service_id']),
            ];
        }else {
            throw new HttpException(422, Json::encode($modelCaller->errors));
        }
    }

    public function actionHoldReciveDrug(){
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        $data = $bodyParams['data'];
        $formData = $bodyParams['formData'];
        $modelProfile = $this->findModelServiceProfile($formData['service_profile_id']);
        $modelProfile->service_id = CoreUtility::string2Array($modelProfile['service_id']);
        $modelQue = $this->findModelQue($data['que_ids']);
        $modelCaller = $this->findModelCaller($data['caller_ids']);
        $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
        $modelCaller->setAttributes([
            'call_status' => TbCaller::STATUS_HOLD,
        ]);
        if($modelCaller->save()){
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(200);
            return [
                'data' => $data,
                'modelQue' => $modelQue,
                'modelProfile' => $modelProfile,
                'formData' => $formData,
                'modelCaller' => $modelCaller,
                'modelQtrans' => $modelQtrans,
            ];
        }else {
            throw new HttpException(422, Json::encode($modelCaller->errors));
        }
    }

    public function actionEndReciveDrug(){
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        $data = $bodyParams['data'];
        $formData = $bodyParams['formData'];
        $modelProfile = $this->findModelServiceProfile($formData['service_profile_id']);
        $modelProfile->service_id = CoreUtility::string2Array($modelProfile['service_id']);
        $modelQue = $this->findModelQue($data['que_ids']);
        $modelCaller = $this->findModelCaller($data['caller_ids']);
        $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
        $modelQtrans->setAttributes([
            'que_trans_status' => TbQtrans::STATUS_ACTIVE,
        ]);
        $modelQue->setAttributes([
            'que_status' => 5,
        ]);
        if($modelQtrans->save() && $modelQue->save()){
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(200);
            return [
                'data' => $data,
                'modelQue' => $modelQue,
                'modelProfile' => $modelProfile,
                'formData' => $formData,
                'modelCaller' => $modelCaller,
                'modelQtrans' => $modelQtrans,
            ];
        }else {
            throw new HttpException(422, Json::encode($modelCaller->errors));
        }
    }

    public function actionDashboardData(){
        $pieData = [];
        $items = [];
        $categories = [];
        $data = [];
        $services = TbService::find()->where(['service_status' => 1])->all();
        $sumall = TbQue::find()->count();
        foreach($services as $service){
            $count = TbQue::find()->where(['service_id' => $service['service_id']])->count();
            $items[] = [
                'service_name' => $service['service_name'],
                'count' => $count,
            ];
            $y = ($count > 0 && $sumall > 0) ? ($count/$sumall)*100 : 0;
            $pieData[] = [
                'name' => $service['service_name'], 'y' => $y
            ];
            $categories = ArrayHelper::merge($categories, [$service['service_name']]);
            $data = ArrayHelper::merge($data, [intval($count)]);
        }
        $response = \Yii::$app->getResponse();
        $response->setStatusCode(200);
        return [
            'pieData' => $pieData,
            'items' => $items,
            'categories' => $categories,
            'dataseries' => $data
        ];
    }

    public function actionQueScanner($qid){
        $modelQue = $this->findModelQue($qid);
        $count = 0;
        if($modelQue['que_status'] == 4){
            $sql = 'SELECT
                COUNT(tb_que.que_ids) as count
                FROM
                tb_que
                INNER JOIN tb_qtrans ON tb_qtrans.que_ids = tb_que.que_ids
                LEFT JOIN tb_caller ON tb_caller.que_trans_ids = tb_qtrans.que_trans_ids
                WHERE
                tb_que.que_status = 4 AND
                tb_que.created_at < :created_at AND
                tb_qtrans.que_trans_type = 2 AND
                tb_qtrans.que_trans_status = 0 AND
                tb_caller.caller_ids IS NULL';
            $params = [
                ':created_at' => $modelQue['created_at'],
            ];
            $count = Yii::$app->db->createCommand($sql)
                    ->bindValues($params)
                    ->queryScalar();
        }elseif($modelQue['que_status'] == 3){
            $sql = 'SELECT
                COUNT(tb_que.que_ids) as count
                FROM
                tb_que
                INNER JOIN tb_qtrans ON tb_qtrans.que_ids = tb_que.que_ids
                LEFT JOIN tb_caller ON tb_caller.que_trans_ids = tb_qtrans.que_trans_ids
                WHERE
                tb_que.que_status = 3 AND
                tb_que.created_at < :created_at AND
                tb_qtrans.que_trans_type = 1 AND
                tb_qtrans.que_trans_status = 0 AND
                tb_caller.caller_ids IS NULL';
            $params = [
                ':created_at' => $modelQue['created_at'],
            ];
            $count = Yii::$app->db->createCommand($sql)
                ->bindValues($params)
                ->queryScalar();
        }
        $response = \Yii::$app->getResponse();
        $response->setStatusCode(200);
        return [
            'count' => $count,
            'modelQue' => $modelQue,
        ];
    }

    protected function getMediaFiles($que_number,$counter_service_id){
        $component = \Yii::createObject([
            'class' => SoundComponent::className(),
            'que_number' => $que_number,
            'counter_id' => $counter_service_id,
        ]);
        return $component->getSource();
    }
}