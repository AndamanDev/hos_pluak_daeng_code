<?php

namespace frontend\modules\app\controllers;

use frontend\modules\app\models\TbCaller;
use frontend\modules\app\models\TbCounterService;
use frontend\modules\app\models\TbQtrans;
use frontend\modules\app\models\TbService;
use frontend\modules\app\models\TbServiceProfile;
use frontend\modules\app\traits\ModelTrait;
use frontend\modules\app\models\TbSoundStation;
use frontend\modules\app\models\TbQue;
use inspinia\utils\CoreUtility;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;
use frontend\modules\app\components\SoundComponent;
use common\classes\AppQuery;

class CallingController extends \yii\web\Controller
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
                        'actions' => ['player','autoplay-media','update-status-callend'],
                        'allow' => true,
                        'roles' => ['@','?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete-que' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCheckDrug()
    {
        $request = Yii::$app->request;
        $modelProfile = new TbServiceProfile();
        $modelProfile->scenario = 'call';

        if ($modelProfile->load($request->post())) {
            if(!empty($modelProfile['service_profile_id'])){
                $modelProfile = $this->findModelServiceProfile($modelProfile['service_profile_id']);
                $modelProfile->setAttributes([
                    'service_id' => CoreUtility::string2Array($modelProfile['service_id']),
                    'counter_service_type_id' => $modelProfile['counter_service_type_id'],
                ]);
                $services = TbService::find()->where(['service_id' => $modelProfile['service_id'], 'service_status' => 1])->asArray()->all();
            }else{
                $modelProfile->setAttributes([
                    'service_id' => null,
                    'counter_service_type_id' => null,
                ]);
            }
            return $this->render('check-drug', [
                'modelProfile' => $modelProfile,
                'services' => $services,
            ]);
        } else {
            $model = $this->findModelServiceProfile(1);
            $modelProfile->setAttributes([
                'service_id' => CoreUtility::string2Array($model['service_id']),
                'counter_service_type_id' => $model['counter_service_type_id'],
                'service_profile_id' => $model['service_profile_id']
            ]);
            $services = TbService::find()->where(['service_id' => $modelProfile['service_id'], 'service_status' => 1])->asArray()->all();
            return $this->render('check-drug', [
                'modelProfile' => $modelProfile,
                'services' => $services,
            ]);
        }
    }

    public function actionChildServiceProfile()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $id = end($_POST['depdrop_parents']);
            if (!empty($id)) {
                $query = $this->findModelServiceProfile($id);
                $list = TbCounterService::find()->andWhere(['counter_service_type_id' => $query['counter_service_type_id'], 'counter_service_status' => 1])->asArray()->all();
                $selected = null;
                if ($id != null && count($list) > 0) {
                    $selected = '';
                    foreach ($list as $i => $counter) {
                        $out[] = ['id' => $counter['counter_service_id'], 'name' => $counter['counter_service_name']];
                        if ($i == 0) {
                            $selected = null;
                        }
                    }
                    // Shows how you can preselect a value
                    echo Json::encode(['output' => $out, 'selected' => $selected]);
                    return;
                }
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionPayment()
    {
        $request = Yii::$app->request;
        $formData = [];
        $modelProfile = new TbServiceProfile();
        $modelProfile->scenario = 'call';

        if ($modelProfile->load($request->post())) {
            $modelProfile = $this->findModelServiceProfile($modelProfile['service_profile_id']);
            $postdata = $request->post('TbServiceProfile', []);
            $attributes = [
                'service_profile_id' => $modelProfile['service_profile_id'],
                'service_id' => CoreUtility::string2Array($modelProfile['service_id']),
                'counter_service_type_id' => $modelProfile['counter_service_type_id'],
                'counter_service_id' => isset($postdata['counter_service_id']) ? $postdata['counter_service_id'] : null,
            ];
            $formData = $attributes;
            $modelProfile->setAttributes($attributes);
            $services = TbService::find()->where(['service_id' => $modelProfile['service_id'], 'service_status' => 1])->asArray()->all();
            return $this->render('payment', [
                'modelProfile' => $modelProfile,
                'services' => $services,
                'formData' => $formData,
            ]);
        }else{
            $model = $this->findModelServiceProfile(2);
            $attributes = [
                'service_profile_id' => $model['service_profile_id'],
                'service_id' => CoreUtility::string2Array($model['service_id']),
                'counter_service_type_id' => $model['counter_service_type_id'],
                'counter_service_id' => null,
            ];
            $formData = $attributes;
            $modelProfile->setAttributes($attributes);
            $services = TbService::find()->where(['service_id' => $modelProfile['service_id'], 'service_status' => 1])->asArray()->all();
            return $this->render('payment', [
                'modelProfile' => $modelProfile,
                'services' => $services,
                'formData' => $formData,
            ]);
        }
    }

    public function actionReciveDrug()
    {
        $request = Yii::$app->request;
        $formData = [];
        $modelProfile = new TbServiceProfile();
        $modelProfile->scenario = 'call';

        if ($modelProfile->load($request->post())) {
            $model = $this->findModelServiceProfile($modelProfile['service_profile_id']);
            $postdata = $request->post('TbServiceProfile', []);
            $attributes = [
                'service_profile_id' => $model['service_profile_id'],
                'service_id' => CoreUtility::string2Array($model['service_id']),
                'counter_service_type_id' => $model['counter_service_type_id'],
                'counter_service_id' => isset($postdata['counter_service_id']) ? $postdata['counter_service_id'] : null,
            ];
            $formData = $attributes;
            $modelProfile->setAttributes($attributes);
            $services = TbService::find()->where(['service_id' => $modelProfile['service_id'], 'service_status' => 1])->asArray()->all();
            return $this->render('recive-drug', [
                'modelProfile' => $modelProfile,
                'services' => $services,
                'formData' => $formData,
            ]);
        } else {
            $model = $this->findModelServiceProfile(3);
            $attributes = [
                'service_profile_id' => $model['service_profile_id'],
                'service_id' => CoreUtility::string2Array($model['service_id']),
                'counter_service_type_id' => $model['counter_service_type_id'],
                'counter_service_id' => null,
            ];
            $formData = $attributes;
            $modelProfile->setAttributes($attributes);
            $services = TbService::find()->where(['service_id' => $modelProfile['service_id'], 'service_status' => 1])->asArray()->all();
            return $this->render('recive-drug', [
                'modelProfile' => $modelProfile,
                'services' => $services,
                'formData' => $formData,
            ]);
        }
    }

    public function actionPlayer()
    {
        $request = Yii::$app->request;
        $model = new TbSoundStation();
        $services = [];
        if($model->load($request->post())){
            $data = $request->post('TbSoundStation',[]);
            if(isset($data['sound_station_id']) && !empty($data['sound_station_id'])){
                $model = $this->findModelSoundStation($data['sound_station_id']);
                $model->counter_service_id = CoreUtility::string2Array($model['counter_service_id']);
                $services = (new \yii\db\Query())
                ->select([
                    'tb_counter_service.counter_service_name',
                    'tb_counter_service_type.counter_service_type_name'
                ])
                ->from('tb_counter_service')
                ->innerJoin('tb_counter_service_type','tb_counter_service_type.counter_service_type_id = tb_counter_service.counter_service_type_id')
                ->where(['counter_service_id' => $model['counter_service_id'], 'counter_service_status' => 1])
                ->all();
            }
        }
        return $this->render('player',['model' => $model,'services' => $services]);
    }


    #ข้อมูลรายการคิว ตรวจสอบยา
    public function actionDataWaitCheckdrug()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!$request->post('data')) {
                return ['data' => []];
            }
            $data = AppQuery::getDataWaitCheckdrug($request->post('data'));
            return ['data' => $data];
            /* $response = Yii::$app->api->callApiData('POST', 'que/data-wait-checkdrug', $request->post('data'));
            if ($response->isOk) {
                return ['data' => $response->data['data']];
            } else {
                if (isset($response->data['message'])) {
                    throw new HttpException($response->data['status'], $response->data['message']);
                } else {
                    throw new HttpException($response->data['status'], $response->data['data']['message']);
                }
            } */
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    #ข้อมูลคิวรอยานาน
    public function actionDataWaitDrugCheckdrug()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!$request->post('data')) {
                return ['data' => []];
            }
            $data = AppQuery::getDataWaitDrugCheckdrug($request->post('data'));
            return ['data' => $data];
            /* $response = Yii::$app->api->callApiData('POST', 'que/data-wait-drug-checkdrug', $request->post('data'));
            if ($response->isOk) {
                return ['data' => $response->data['data']];
            } else {
                if (isset($response->data['message'])) {
                    throw new HttpException($response->data['status'], $response->data['message']);
                } else {
                    throw new HttpException($response->data['status'], $response->data['data']['message']);
                }
            } */
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    #ข้อมูลคิวรอชำระเงิน
    public function actionDataWaitPaymentCheckdrug()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!$request->post('data')) {
                return ['data' => []];
            }
            $data = AppQuery::getDataWaitPaymentCheckdrug($request->post('data'));
            return ['data' => $data];
            /* $response = Yii::$app->api->callApiData('POST', 'que/data-wait-payment-checkdrug', $request->post('data'));
            if ($response->isOk) {
                return ['data' => $response->data['data']];
            } else {
                if (isset($response->data['message'])) {
                    throw new HttpException($response->data['status'], $response->data['message']);
                } else {
                    throw new HttpException($response->data['status'], $response->data['data']['message']);
                }
            } */
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    #คิวรอเรียกการเงิน
    public function actionDataWaitingPayment()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!$request->post('data')) {
                return ['data' => []];
            }
            $data = AppQuery::getDataWaitingPayment($request->post('data'));
            return ['data' => $data];
            /* $response = Yii::$app->api->callApiData('POST', 'que/data-waiting-payment', $request->post('data'));
            if ($response->isOk) {
                return ['data' => $response->data['data']];
            } else {
                if (isset($response->data['message'])) {
                    throw new HttpException($response->data['status'], $response->data['message']);
                } else {
                    throw new HttpException($response->data['status'], $response->data['data']['message']);
                }
            } */
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    #คิวกำลังเรียกการเงิน
    public function actionDataCallingPayment()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!$request->post('data')) {
                return ['data' => []];
            }
            $data = AppQuery::getDataCallingPayment(['data' => $request->post('data'),'formData' => $request->post('formData')]);
            return ['data' => $data];
            /* $response = Yii::$app->api->callApiData('POST', 'que/data-calling-payment', ['data' => $request->post('data'),'formData' => $request->post('formData')]);
            if ($response->isOk) {
                return ['data' => $response->data['data']];
            } else {
                if (isset($response->data['message'])) {
                    throw new HttpException($response->data['status'], $response->data['message']);
                } else {
                    throw new HttpException($response->data['status'], $response->data['data']['message']);
                }
            } */
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    #พักคิว การเงิน
    public function actionDataHoldPayment()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!$request->post('data')) {
                return ['data' => []];
            }
            $data = AppQuery::getDataHoldPayment(['data' => $request->post('data'),'formData' => $request->post('formData')]);
            return ['data' => $data];
            /* $response = Yii::$app->api->callApiData('POST', 'que/data-hold-payment', ['data' => $request->post('data'),'formData' => $request->post('formData')]);
            if ($response->isOk) {
                return ['data' => $response->data['data']];
            } else {
                if (isset($response->data['message'])) {
                    throw new HttpException($response->data['status'], $response->data['message']);
                } else {
                    throw new HttpException($response->data['status'], $response->data['data']['message']);
                }
            } */
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    #คิวรอเรียก รับยา
    public function actionDataWaitingRecive()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!$request->post('data')) {
                return ['data' => []];
            }
            $data = AppQuery::getDataWaitingRecive($request->post('data'));
            return ['data' => $data];
            /* $response = Yii::$app->api->callApiData('POST', 'que/data-waiting-recive', $request->post('data'));
            if ($response->isOk) {
                return ['data' => $response->data['data']];
            } else {
                if (isset($response->data['message'])) {
                    throw new HttpException($response->data['status'], $response->data['message']);
                } else {
                    throw new HttpException($response->data['status'], $response->data['data']['message']);
                }
            } */
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    #คิวกำลังเรียก รับยา
    public function actionDataCallingRecive()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!$request->post('data')) {
                return ['data' => []];
            }
            $data = AppQuery::getDataCallingRecive(['data' => $request->post('data'),'formData' => $request->post('formData')]);
            return ['data' => $data];
            /* $response = Yii::$app->api->callApiData('POST', 'que/data-calling-recive', ['data' => $request->post('data'),'formData' => $request->post('formData')]);
            if ($response->isOk) {
                return ['data' => $response->data['data']];
            } else {
                if (isset($response->data['message'])) {
                    throw new HttpException($response->data['status'], $response->data['message']);
                } else {
                    throw new HttpException($response->data['status'], $response->data['data']['message']);
                }
            } */
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    #รายการพักคิว รับยา
    public function actionDataHoldRecive()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!$request->post('data')) {
                return ['data' => []];
            }
            $data = AppQuery::getDataHoldRecive(['data' => $request->post('data'),'formData' => $request->post('formData')]);
            return ['data' => $data];
            /* $response = Yii::$app->api->callApiData('POST', 'que/data-hold-recive', ['data' => $request->post('data'),'formData' => $request->post('formData')]);
            if ($response->isOk) {
                return ['data' => $response->data['data']];
            } else {
                if (isset($response->data['message'])) {
                    throw new HttpException($response->data['status'], $response->data['message']);
                } else {
                    throw new HttpException($response->data['status'], $response->data['data']['message']);
                }
            } */
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    /* ########## Delete ########## */
    public function actionDeleteQue($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModelQue($id);
        $model->delete();
        TbQtrans::deleteAll(['que_ids' => $id]);

        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true];
        } else {
            return $this->redirect(['ticket']);
        }
    }

    /* ########## Create , Update ########## */

    public function actionUpdateStatusCheckdrug($que_ids)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $postdata = $request->post();
            $data = $request->post('data', []);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $modelQue = $this->findModelQue($que_ids);

                if ($postdata['type'] == 'payment') { #ชำระเงิน
                    $que_trans_type = TbQtrans::TYPE_PAYMENT;
                    $que_status = 3;
                }
                if ($postdata['type'] == 'not-payment') { #ไม่ชำระเงิน
                    $que_trans_type = TbQtrans::TYPE_RECIVE_DRUG;
                    $que_status = 4;
                }
                if ($postdata['type'] == 'waiting-drug') { #รอยานาน
                    $que_trans_type = TbQtrans::TYPE_WAITING_DRUG;
                    $que_status = 2;
                }

                if(TbQtrans::TYPE_WAITING_DRUG == $que_trans_type){
                    $modelQue->setAttributes([
                        'que_status' => $que_status,
                    ]);
                    if ($modelQue->save()) {
                        $transaction->commit();
                        return [
                            'status' => 200,
                            'data' => $data,
                            'modelQue' => $modelQue,
                            'modelProfile' => $postdata['modelProfile'],
                        ];
                    } else {
                        $transaction->rollBack();
                        throw new HttpException(422, Json::encode($modelQue->errors));
                    }
                }else{
                    $modelQtrans = new TbQtrans();
                    $modelQtrans->setAttributes([
                        'que_trans_type' => $que_trans_type,
                        'que_ids' => $que_ids,
                        'que_trans_status' => TbQtrans::STATUS_UNACTIVE,
                    ]);
                    $modelQue->setAttributes([
                        'que_status' => $que_status,
                    ]);
                    if ($modelQue->save() && $modelQtrans->save()) {
                        $transaction->commit();
                        return [
                            'status' => 200,
                            'data' => $data,
                            'modelQue' => $modelQue,
                            'modelQtrans' => $modelQtrans,
                            'modelProfile' => $postdata['modelProfile'],
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
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionCallPayment($que_ids)
    {
        $request = Yii::$app->request;
        $modelQue = $this->findModelQue($que_ids);
        if ($request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $data = $request->post('data');
            $modelProfile = $request->post('modelProfile');
            $formData = $request->post('formData');
            $modelCaller = new TbCaller();
            $modelCaller->setAttributes([
                'que_ids' => $que_ids,
                'que_trans_ids' => $data['que_trans_ids'],
                'service_profile_id' => $formData['service_profile_id'],
                'counter_service_id' => $formData['counter_service_id'],
                'call_timestp' => Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s'),
                'call_status' => TbCaller::STATUS_CALLING #เรียกคิว
            ]);
            if ($modelCaller->save()) {
                $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
                $modelCounterService = $this->findModelCounterService($formData['counter_service_id']);
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
        } else {
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    #เรียกคิวซ้ำ การเงิน
    public function actionRecallPayment($caller_ids){
        $request = Yii::$app->request;
        if($request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $data = $request->post('data');
            $modelProfile = $request->post('modelProfile');
            $formData = $request->post('formData');
            $modelQue = $this->findModelQue($data['que_ids']);
            $modelCaller = $this->findModelCaller($caller_ids);
            $modelCaller->setAttributes([
                'service_profile_id' => $formData['service_profile_id'],
                'counter_service_id' => $formData['counter_service_id'],
                'call_timestp' => Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s'),
                'call_status' => TbCaller::STATUS_CALLING,
            ]);
            $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
            $modelCounterService = $this->findModelCounterService($formData['counter_service_id']);
            if($modelCaller->save()){
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
        }else{
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionHoldPayment($caller_ids){
        $request = Yii::$app->request;
        if($request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $data = $request->post('data');
            $modelProfile = $request->post('modelProfile');
            $formData = $request->post('formData');
            $modelQue = $this->findModelQue($data['que_ids']);
            $modelCaller = $this->findModelCaller($caller_ids);
            $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
            $modelCaller->setAttributes([
                'call_status' => TbCaller::STATUS_HOLD,
            ]);
            if($modelCaller->save()){
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
        }else{
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionEndPayment($caller_ids){
        $request = Yii::$app->request;
        if($request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $data = $request->post('data');
            $modelProfile = $request->post('modelProfile');
            $formData = $request->post('formData');

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $modelQue = $this->findModelQue($data['que_ids']);
                $modelQue->setAttributes([
                    'que_status' => 4, //รอรับยา
                    'payment_at' => Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s')
                ]);
                $modelCaller = $this->findModelCaller($caller_ids);
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
        }else{
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionCallReciveSelected(){
        $request = Yii::$app->request;
        if($request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $data = $request->post('data');
            $modelProfile = $request->post('modelProfile');
            $formData = $request->post('formData');
            $transaction = Yii::$app->db->beginTransaction();
            $response = [];
            $success = false;
            try {
                foreach($data as $key => $item){
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
                        $response[] = [
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
                    return $response;
                }else{
                    $transaction->rollBack();
                    throw new HttpException(422, "เกิดข้อผิดพลาด!");
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }else{
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionCallRecive($que_ids){
        $request = Yii::$app->request;
        if($request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $data = $request->post('data');
            $modelProfile = $request->post('modelProfile');
            $formData = $request->post('formData');
            $modelQue = $this->findModelQue($que_ids);
            $modelCaller = new TbCaller();
            $modelCaller->setAttributes([
                'que_ids' => $que_ids,
                'que_trans_ids' => $data['que_trans_ids'],
                'service_profile_id' => $formData['service_profile_id'],
                'counter_service_id' => $formData['counter_service_id'],
                'call_timestp' => Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s'),
                'call_status' => TbCaller::STATUS_CALLING #เรียกคิว
            ]);
            if ($modelCaller->save()) {
                $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
                $modelCounterService = $this->findModelCounterService($formData['counter_service_id']);
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
        }else{
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionRecallReciveDrug($caller_ids){
        $request = Yii::$app->request;
        if($request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $data = $request->post('data');
            $modelProfile = $request->post('modelProfile');
            $formData = $request->post('formData');
            $modelQue = $this->findModelQue($data['que_ids']);
            $modelCaller = $this->findModelCaller($caller_ids);
            $modelCaller->setAttributes([
                'service_profile_id' => $formData['service_profile_id'],
                'counter_service_id' => $formData['counter_service_id'],
                'call_timestp' => Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s'),
                'call_status' => TbCaller::STATUS_CALLING,
            ]);
            $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
            $modelCounterService = $this->findModelCounterService($formData['counter_service_id']);
            if($modelCaller->save()){
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
        }else{
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionHoldReciveDrug($caller_ids){
        $request = Yii::$app->request;
        if($request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $data = $request->post('data');
            $modelProfile = $request->post('modelProfile');
            $formData = $request->post('formData');
            $modelQue = $this->findModelQue($data['que_ids']);
            $modelCaller = $this->findModelCaller($caller_ids);
            $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
            $modelCaller->setAttributes([
                'call_status' => TbCaller::STATUS_HOLD,
            ]);
            if($modelCaller->save()){
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
        }else{
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionEndReciveDrug($caller_ids){
        $request = Yii::$app->request;
        if($request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $data = $request->post('data');
            $modelProfile = $request->post('modelProfile');
            $formData = $request->post('formData');
            $modelQue = $this->findModelQue($data['que_ids']);
            $modelCaller = $this->findModelCaller($caller_ids);
            $modelQtrans = $this->findModelQtrans($modelCaller['que_trans_ids']);
            $modelQtrans->setAttributes([
                'que_trans_status' => TbQtrans::STATUS_ACTIVE,
            ]);
            $modelQue->setAttributes([
                'que_status' => 5,
            ]);
            if($modelQtrans->save() && $modelQue->save()){
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
        }else{
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionEndRecive($que_ids){
        $request = Yii::$app->request;
        if($request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $data = $request->post('data');
                $modelProfile = $request->post('modelProfile');
                $formData = $request->post('formData');
                $modelQue = $this->findModelQue($que_ids);
                $modelCounterService = $this->findModelCounterService($formData['counter_service_id']);

                $modelCaller = new TbCaller();
                $modelCaller->setAttributes([
                    'que_ids' => $que_ids,
                    'que_trans_ids' => $data['que_trans_ids'],
                    'service_profile_id' => $formData['service_profile_id'],
                    'counter_service_id' => $formData['counter_service_id'],
                    'call_timestp' => Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s'),
                    'call_status' => TbCaller::STATUS_CALLEND,
                ]);
                $modelQue->setAttributes([
                    'que_status' => 5,
                ]);
                $modelQtrans = $this->findModelQtrans($data['que_trans_ids']);
                $modelQtrans->setAttributes([
                    'que_trans_status' => TbQtrans::STATUS_ACTIVE,
                ]);
                if ($modelCaller->save() && $modelQue->save() && $modelQtrans->save()) {
                    $transaction->commit();
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
                    $transaction->rollBack();
                    throw new HttpException(422, Json::encode($modelCaller->errors));
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
            
        }else{
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionUpdateStatusCallend($caller_ids){
        $request = Yii::$app->request;
        if($request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $modelCaller = $this->findModelCaller($caller_ids);
            $modelCaller->call_status = TbCaller::STATUS_CALLEND;
            $modelCaller->save(false);
            return $modelCaller;
        }else{
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionAutoplayMedia(){
        $request = Yii::$app->request;
        $response =  [];
        if($request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $data = $request->post();
            if($data){
                $query = TbCaller::find()->where(['call_status' => TbCaller::STATUS_CALLING,'counter_service_id' => $data['counter_service_id']])->orderBy(['call_timestp' => SORT_ASC])->all();
                foreach($query as $item){
                    $modelCounterService = $this->findModelCounterService($item['counter_service_id']);
                    $response[] = [
                        'data' => [],
                        'modelQue' => $item->que,
                        'modelProfile' => $item->serviceProfile,
                        'formData' => [],
                        'modelCaller' => $item,
                        'modelQtrans' => $item->qtrans,
                        'modelCounterService' => $modelCounterService,
                        'media_files' => $this->getMediaFiles($item->que->que_num,$item['counter_service_id']),
                    ];
                }
            }
            return $response;
        }else{
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
    }

    public function actionRecheck($que_ids, $que_trans_ids)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $modelQtrans = $this->findModelQtrans($que_trans_ids);
            $modelQtrans->delete();
            $modelQue = $this->findModelQue($que_ids);
            $modelQue->setAttributes([
                'que_status' => TbQue::STATUS_PRINT,
            ]);
            if($modelQue->save()){
                return [
                    'modelQue' => $modelQue,
                ];
            }else {
                throw new HttpException(422, Json::encode($modelCaller->errors));
            }
        }else{
            throw new BadRequestHttpException(Yii::t('app', 'The system could not process your request. Please check and try again.'));
        }
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
