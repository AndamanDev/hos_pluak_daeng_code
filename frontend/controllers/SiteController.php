<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\modules\app\models\TbService;
use frontend\modules\app\models\TbQue;
use yii\helpers\ArrayHelper;
use frontend\modules\app\traits\ModelTrait;
use frontend\modules\app\models\TbQtrans;
use frontend\modules\app\models\TbCaller;
use frontend\modules\app\models\Report;
use frontend\modules\app\models\TbQueData;
/**
 * Site controller
 */
class SiteController extends Controller
{
    use ModelTrait;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup','index'],
                'rules' => [
                    [
                        'actions' => ['signup','index','mobile-view'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout','index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'glide' => 'trntv\glide\actions\GlideAction',
            'download' => 'inspinia\components\DownloadAction'
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new Report();
        $data = [];
        $posted = $request->post('Report',[]);
        $services = TbService::find()->where(['service_status' => 1])->all();
        $items = [];
        $pieData = [];
        $series = [];
        $categories = [];
        $data = [];
        
        if($model->load($request->post())){
            $from_date = $posted['from_date'].' 00:00:00';
            $to_date = $posted['to_date'].' 23:59:59';
            /* $periods = new \DatePeriod(
                new \DateTime($from_date),
                new \DateInterval('P1D'),
                new \DateTime(date('Y-m-d',strtotime('+1 day', strtotime($to_date))))
            ); */
            foreach($services as $service){
                $sumall = TbQueData::find()->andWhere(['between', 'tb_que_data.created_at', $from_date, $to_date])->count();
                $count = TbQueData::find()
                ->where(['service_id' => $service['service_id']])
                ->andWhere(['between', 'tb_que_data.created_at', $from_date, $to_date])
                ->count();
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
        }else{
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
        }
        $series[] = [
            'name' => 'คิวทั้งหมด',
            'colorByPoint'=> true,
            'data' => $data,
        ];
        
        return $this->render('index',[
            'items' => $items,
            'pieData' => $pieData,
            'series' => $series,
            'categories' => $categories,
            'posted' => $posted,
            'model' => $model,
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionClearCache() {
        $frontendAssetPath = Yii::getAlias('@frontend') . '/web/assets/';
        $backendAssetPath = Yii::getAlias('@backend') . '/web/assets/';

        $this->recursiveDelete($frontendAssetPath);
        $this->recursiveDelete($backendAssetPath);

        if (Yii::$app->cache->flush()) {
            Yii::$app->session->setFlash('crudMessage', 'Cache has been flushed.');
        } else {
            Yii::$app->session->setFlash('crudMessage', 'Failed to flush cache.');
        }

        return Yii::$app->getResponse()->redirect(Yii::$app->getRequest()->referrer);
    }

    public static function recursiveDelete($path) {
        if (is_file($path)) {
            return @unlink($path);
        } elseif (is_dir($path)) {
            $scan = glob(rtrim($path, '/') . '/*');
            foreach ($scan as $index => $newPath) {
                self::recursiveDelete($newPath);
            }
            return @rmdir($path);
        }
    }

    public function actionMobileView($view_id)
    {
        $modelQue = $this->findModelQue($view_id);
        $service_name = '-';
        $countername = '';
        $count = 0;
        if($modelQue['que_status'] == 1){
            $service_name = 'รอตรวจสอบยา';
            $params = [':created_at' => $modelQue['created_at']];
            $count = Yii::$app->db->createCommand('SELECT
                Count(tb_que.que_ids) AS count
                FROM
                tb_que
                INNER JOIN tb_que_status ON tb_que_status.que_status_id = tb_que.que_status
                INNER JOIN tb_service ON tb_service.service_id = tb_que.service_id
                INNER JOIN tb_service_group ON tb_service_group.service_group_id = tb_service.service_group_id
                WHERE
                tb_que.que_status = 1 AND
                tb_que.created_at < :created_at
                ORDER BY
                tb_que.created_at ASC')
                ->bindValues($params)
                ->queryScalar();
        }elseif($modelQue['que_status'] == 2){
            $service_name = 'รอยานาน';
            $params = [':created_at' => $modelQue['created_at']];
            $count = Yii::$app->db->createCommand('SELECT
                Count(tb_que.que_ids) AS count
                FROM
                tb_que
                INNER JOIN tb_que_status ON tb_que_status.que_status_id = tb_que.que_status
                INNER JOIN tb_service ON tb_service.service_id = tb_que.service_id
                INNER JOIN tb_service_group ON tb_service_group.service_group_id = tb_service.service_group_id
                WHERE
                tb_que.que_status = 2 AND
                tb_que.created_at < :created_at
                ORDER BY
                tb_que.created_at ASC')
                ->bindValues($params)
                ->queryScalar();
        }elseif($modelQue['que_status'] == 3){
            $modelQtran = TbQtrans::findOne(['que_trans_type' => 1, 'que_ids' => $view_id]);
            $modelCaller = TbCaller::findOne(['que_ids' => $view_id,'que_trans_ids' => $modelQtran['que_trans_ids']]);
            if($modelCaller){
                $counter = $modelCaller->counterService;
                $countername = $counter->counter_service_name;
                $service_name = 'ชำระเงิน';
            }else{
                $service_name = 'รอชำระเงิน';
            }
            $params = [':created_at' => $modelQue['created_at']];
            $count = Yii::$app->db->createCommand('SELECT
                Count(tb_que.que_ids) as count
                FROM
                tb_que
                INNER JOIN tb_que_status ON tb_que_status.que_status_id = tb_que.que_status
                INNER JOIN tb_service ON tb_service.service_id = tb_que.service_id
                INNER JOIN tb_service_group ON tb_service_group.service_group_id = tb_service.service_group_id
                INNER JOIN tb_qtrans ON tb_qtrans.que_ids = tb_que.que_ids
                LEFT JOIN tb_caller ON tb_caller.que_trans_ids = tb_qtrans.que_trans_ids
                WHERE
                tb_que.que_status = 3 AND
                tb_caller.caller_ids IS NULL AND
                tb_qtrans.que_trans_type = 1 AND
                tb_que.created_at < :created_at
                ORDER BY
                tb_que.created_at ASC')
                ->bindValues($params)
                ->queryScalar();
        }elseif($modelQue['que_status'] == 4){
            $modelQtran = TbQtrans::findOne(['que_trans_type' => 2, 'que_ids' => $view_id]);
            $modelCaller = TbCaller::findOne(['que_ids' => $view_id,'que_trans_ids' => $modelQtran['que_trans_ids']]);
            if($modelCaller){
                $counter = $modelCaller->counterService;
                $countername = $counter->counter_service_name;
                $service_name = 'รับยา';
            }else{
                $service_name = 'รอรับยา';
            }
            $params = [':created_at' => $modelQue['created_at']];
            $count = Yii::$app->db->createCommand('SELECT
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
                tb_que.que_status = 4 AND
                tb_qtrans.que_trans_status = 0 AND
                tb_caller.call_status IN (1, 3) AND
                tb_qtrans.que_trans_type = 2 AND
                tb_que.created_at < :created_at
                ORDER BY
                tb_que.created_at ASC
                ')
                ->bindValues($params)
                ->queryScalar();
        }elseif($modelQue['que_status'] == 5){
            $service_name = 'เสร็จสิ้น!';
        }
        return $this->renderAjax('mobile-view',[
            'modelQue' => $modelQue,
            'service_name' => $service_name,
            'countername' => $countername,
            'count' => $count
        ]);
    }
}
