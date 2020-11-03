<?php
namespace frontend\modules\app\models;

use yii\base\Model;

class Report extends Model {
    public $from_date;
    public $to_date;
    public $times;

    public function rules()
    {
        return [
            [['from_date', 'from_date','times'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'from_date' => 'วันที่',
            'to_date' => 'ถึงวันที่',
            'times' => 'ช่วงเวลา',
        ];
    }
}