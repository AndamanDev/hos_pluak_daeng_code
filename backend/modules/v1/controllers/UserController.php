<?php
namespace backend\modules\v1\controllers;

use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\rest\ActiveController;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

use yii\web\HttpException;
use yii\web\NotFoundHttpException;

use backend\modules\v1\models\LoginForm;
use yii\web\Response;
use que\user\models\Profile;
use dektrium\user\helpers\Timezone;

class UserController extends ActiveController
{
    public $modelClass = 'backend\modules\v1\models\User';

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create'], $actions['update']);

        return $actions;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                //HttpBasicAuth::className(),
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
                'login' => ['post'],
                'info' => ['get'],
                'update-profile' => ['post']
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
            'login',
        ];
        // setup access
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['index', 'view', 'create', 'update', 'delete'], //only be applied to
            'rules' => [
                [
                    'allow' => true,
                    'actions' => [
                        'index', 'view', 'create', 'update', 'delete','info','update-profile'
                    ],
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actionLogin()
    {
        $model = \Yii::createObject(LoginForm::className());
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->validate() && $model->login()) {
            $user = $model->getUser();
            $user->generateAccessTokenAfterUpdatingClientInfo(true);

            $response = \Yii::$app->getResponse();
            $response->setStatusCode(200);

            $responseData = [
                'access_token' => $user->access_token,
            ];

            return $responseData;
        } else {
            // Validation error
            throw new HttpException(422, Json::encode($model->errors));
        }
    }

    public function actionInfo() {
        $user = Profile::findOne(['user_id' => \Yii::$app->user->getId()]);
        $timezoneData = ArrayHelper::map(
            Timezone::getAll(),
            'timezone',
            'name'
        );
        $timezones = [];

        foreach($timezoneData as $key => $item){
            if (strpos($item, 'Asia') !== false) {
                $timezones[] = [
                    'name' => $item,
                    'value' => $key
                ];
            }
        }

        if($user) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(200);

            return [
                'profile'  =>  [
                    'name' => $user->name,
                    'public_email' => $user->public_email,
                    'website' => $user->website,
                    'timezone' => $user->timezone,
                    'bio' => $user->bio
                ],
                'timezones' => $timezones
            ];
        } else {
            // Validation error
            throw new NotFoundHttpException("Object not found");
        }
    }

    public function actionUpdateProfile() {
        $model = Profile::findOne(['user_id' => \Yii::$app->user->getId()]);

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->validate() && $model->save()) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(200);
        } else {
            // Validation error
            throw new HttpException(422, json_encode($model->errors));
        }

        return $model;
    }
}
