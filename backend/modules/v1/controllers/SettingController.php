<?php
namespace backend\modules\v1\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\Response;
use inspinia\widgets\tablecolumn\ActionTable;
use inspinia\widgets\tablecolumn\ColumnTable;
use inspinia\widgets\tablecolumn\ColumnData;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use kartik\icons\Icon;
use frontend\modules\app\models\TbSound;
use inspinia\utils\CoreUtility;
use frontend\modules\app\models\TbCounterService;
use frontend\modules\app\models\TbService;
use yii\helpers\Html;
use common\classes\AppQuery;

class SettingController extends ActiveController
{
    public $modelClass = '';

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
                'data-service-group' => ['get'],
                'data-ticket' => ['get'],
                'data-counter-service' => ['get'],
                'data-sound' => ['get'],
                'data-sound-station' => ['get'],
                'data-service-profile' => ['get'],
                'data-display' => ['get']
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
        ];
        // setup access
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['index', 'view', 'create', 'update', 'delete'], //only be applied to
            'rules' => [
                [
                    'allow' => true,
                    'actions' => [
                        'index', 'view', 'create', 'update', 'delete', 'data-service-group', 'data-ticket', 'data-counter-service',
                        'data-sound', 'data-sound-station','data-service-profile','data-display'
                    ],
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actionDataServiceGroup()
    {
        $data = AppQuery::DataServiceGroup();
        return $data;
    }

    public function actionDataTicket()
    {
        $data = AppQuery::getDataTicket();
        return $data;
    }

    public function actionDataCounterService()
    {
        $data = AppQuery::getDataCounterService();
        return $data;
    }

    public function actionDataSound()
    {
        $data = AppQuery::getDataSound();
        return $data;
    }

    public function actionDataSoundStation()
    {
        $data = AppQuery::getDataSoundStation();
        return $data;
    }

    public function actionDataServiceProfile()
    {
        $data = AppQuery::getDataServiceProfile();
        return $data;
    }

    public function actionDataDisplay()
    {
        $data = AppQuery::getDataDisplay();
        return $data;
    }


    protected function getBadgeStatus($status){
        if($status == 0){
            return \kartik\helpers\Html::badge(Icon::show('close').' ปิดใช้งาน',['class' => 'badge badge-danger']);
        }elseif($status == 1){
            return \kartik\helpers\Html::badge(Icon::show('check').' เปิดใช้งาน',['class' => 'badge badge-primary']);
        }
    }

    protected function getSoundname($id){
        $model = TbSound::findOne($id);
        if($model){
            return $model['sound_th'];
        }else{
            return '-';
        }
    }

    protected function getCounterServiceName($json){
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

    protected function getServiceNames($json){
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
}
