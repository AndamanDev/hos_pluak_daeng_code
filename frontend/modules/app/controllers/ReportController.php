<?php

namespace frontend\modules\app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use frontend\modules\app\models\Report;

class ReportController extends \yii\web\Controller
{
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

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new Report();
        $data = [];
        $posted = $request->post('Report',[]);
        if($model->load($request->post())){
            $from_date = $posted['from_date'];
            $to_date = $posted['to_date'];
            $times = $posted['times'];
            $periods = new \DatePeriod(
                new \DateTime($from_date),
                new \DateInterval('P1D'),
                new \DateTime(date('Y-m-d',strtotime('+1 day', strtotime($to_date))))
            );
            foreach ($periods as $key => $value) {
                $date = $value->format('Y-m-d');
                if(count($times) > 0){
                    foreach($times as $t){
                        $timeRange = $t['time_start'].'-'.$t['time_end'];
                        $time_start = $date.' '.$t['time_start'];
                        $time_end = $date.' '.$t['time_end'];
                        $query = (new \yii\db\Query())
                            ->select([
                                'tb_que_data.que_ids',
                                'tb_que_data.que_num',
                                'tb_que_data.created_at',
                                'tb_qtrans_data.que_trans_type',
                                'tb_caller_data.call_timestp',
                                'tb_counter_service.counter_service_name',
                                'tb_service.service_name',
                                'tb_service_profile.service_profile_name',
                                'MINUTE(TIMEDIFF(tb_que_data.created_at, tb_caller_data.call_timestp)) AS t_wait',
                            ])
                            ->from('tb_que_data')
                            ->innerJoin('tb_qtrans_data', 'tb_qtrans_data.que_ids = tb_que_data.que_ids')
                            ->innerJoin('tb_caller_data', 'tb_caller_data.que_trans_ids = tb_qtrans_data.que_trans_ids')
                            ->innerJoin('tb_counter_service', 'tb_counter_service.counter_service_id = tb_caller_data.counter_service_id')
                            ->innerJoin('tb_service', 'tb_service.service_id = tb_que_data.service_id')
                            ->innerJoin('tb_service_profile', 'tb_service_profile.service_profile_id = tb_caller_data.service_profile_id')
                            ->where(['tb_qtrans_data.que_trans_type' => 1])
                            ->andWhere(['between', 'tb_que_data.created_at', $time_start, $time_end])
                            ->all();
                        if($query){
                            foreach($query as $item) {
                                $arr = [
                                    'que_num' => $item['que_num'],
                                    'created_at' => $item['created_at'],
                                    'call_timestp' => $item['call_timestp'],
                                    'service_name' => $item['service_name'],
                                    't_wait' => $item['t_wait'],
                                    'time_range' => $timeRange,
                                    'day' => $date
                                ];
                                $data[] = $arr;
                            }
                        }else{
                            $arr = [
                                'que_num' => '',
                                'created_at' => null,
                                'call_timestp' => null,
                                'service_name' => '',
                                't_wait' => '',
                                'time_range' => $timeRange,
                                'day' => $date
                            ];
                            $data[] = $arr;
                        }
                    }
                }
            }
        }else{
            $times = [
                ['time_start' => '06:00','time_end' => '07:00'],
                ['time_start' => '07:00','time_end' => '08:00'],
                ['time_start' => '08:00','time_end' => '09:00'],
                ['time_start' => '09:00','time_end' => '10:00'],
                ['time_start' => '10:00','time_end' => '11:00'],
                ['time_start' => '11:00','time_end' => '12:00'],
                ['time_start' => '12:00','time_end' => '13:00'],
                ['time_start' => '13:00','time_end' => '14:00'],
                ['time_start' => '14:00','time_end' => '15:00'],
                ['time_start' => '15:00','time_end' => '16:00'],
            ];
            $model->times = $times;
        }
        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => false,
            ],
        ]);
        return $this->render('index',[
            'dataProvider' => $dataProvider,
            'model' => $model,
            'posted' => $posted,
        ]);
    }

    public function actionReciveDrug()
    {
        $request = Yii::$app->request;
        $model = new Report();
        $data = [];
        $posted = $request->post('Report',[]);
        if($model->load($request->post())){
            $from_date = $posted['from_date'];
            $to_date = $posted['to_date'];
            $times = $posted['times'];
            $periods = new \DatePeriod(
                new \DateTime($from_date),
                new \DateInterval('P1D'),
                new \DateTime(date('Y-m-d',strtotime('+1 day', strtotime($to_date))))
            );
            foreach ($periods as $key => $value) {
                $date = $value->format('Y-m-d');
                if(count($times) > 0){
                    foreach($times as $t){
                        $timeRange = $t['time_start'].'-'.$t['time_end'];
                        $time_start = $date.' '.$t['time_start'];
                        $time_end = $date.' '.$t['time_end'];
                        $query = (new \yii\db\Query())
                            ->select([
                                'tb_que_data.que_ids',
                                'tb_que_data.que_num',
                                'tb_que_data.created_at',
                                'tb_qtrans_data.que_trans_type',
                                'tb_qtrans_data.created_at AS tb_qtrans_time',
                                'tb_caller_data.call_timestp',
                                'tb_counter_service.counter_service_name',
                                'tb_service.service_name',
                                'tb_service_profile.service_profile_name',
                                'MINUTE(TIMEDIFF(tb_que_data.created_at, tb_qtrans_data.created_at)) AS t_wait1',
                                'MINUTE(TIMEDIFF(tb_qtrans_data.created_at, tb_caller_data.call_timestp)) AS t_wait2'
                            ])
                            ->from('tb_que_data')
                            ->innerJoin('tb_qtrans_data', 'tb_qtrans_data.que_ids = tb_que_data.que_ids')
                            ->innerJoin('tb_caller_data', 'tb_caller_data.que_trans_ids = tb_qtrans_data.que_trans_ids')
                            ->innerJoin('tb_counter_service', 'tb_counter_service.counter_service_id = tb_caller_data.counter_service_id')
                            ->innerJoin('tb_service', 'tb_service.service_id = tb_que_data.service_id')
                            ->innerJoin('tb_service_profile', 'tb_service_profile.service_profile_id = tb_caller_data.service_profile_id')
                            ->where(['tb_qtrans_data.que_trans_type' => 2])
                            ->andWhere(['between', 'tb_que_data.created_at', $time_start, $time_end])
                            ->all();
                        if($query){
                            foreach($query as $item) {
                                $arr = [
                                    'que_num' => $item['que_num'],
                                    'created_at' => $item['created_at'],
                                    'call_timestp' => $item['call_timestp'],
                                    'service_name' => $item['service_name'],
                                    't_wait1' => $item['t_wait1'],
                                    't_wait2' => $item['t_wait2'],
                                    'time_range' => $timeRange,
                                    'day' => $date,
                                    'tb_qtrans_time' => $item['tb_qtrans_time'],
                                ];
                                $data[] = $arr;
                            }
                        }else{
                            $arr = [
                                'que_num' => '',
                                'created_at' => null,
                                'call_timestp' => null,
                                'service_name' => '',
                                't_wait1' => '',
                                't_wait2' => '',
                                'time_range' => $timeRange,
                                'day' => $date,
                                'tb_qtrans_time' => null,
                            ];
                            $data[] = $arr;
                        }
                    }
                }
            }
        }else{
            $times = [
                ['time_start' => '06:00','time_end' => '07:00'],
                ['time_start' => '07:00','time_end' => '08:00'],
                ['time_start' => '08:00','time_end' => '09:00'],
                ['time_start' => '09:00','time_end' => '10:00'],
                ['time_start' => '10:00','time_end' => '11:00'],
                ['time_start' => '11:00','time_end' => '12:00'],
                ['time_start' => '12:00','time_end' => '13:00'],
                ['time_start' => '13:00','time_end' => '14:00'],
                ['time_start' => '14:00','time_end' => '15:00'],
                ['time_start' => '15:00','time_end' => '16:00'],
            ];
            $model->times = $times;
        }
        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => false,
            ],
        ]);
        return $this->render('recive-drug',[
            'dataProvider' => $dataProvider,
            'model' => $model,
            'posted' => $posted,
        ]);
    }

}
