<?php

namespace frontend\modules\app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use inspinia\behaviors\CoreMultiValueBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use frontend\modules\app\traits\ModelTrait;
/**
 * This is the model class for table "tb_que".
 *
 * @property int $que_ids running
 * @property string $que_num หมายเลขคิว
 * @property string $que_vn Visit number ของผู้ป่วย
 * @property string $que_hn หมายเลข HN ผู้ป่วย
 * @property string $pt_name ชื่อผู้ป่วย
 * @property int $service_id ประเภทบริการ
 * @property int $service_group_id กลุ่มบริการ
 * @property int $que_status สถานะ
 * @property string $created_at วันที่บันทึก
 * @property string $updated_at วันที่แก้ไข
 * @property int $created_by ผู้บันทึก
 * @property int $updated_by ผู้แก้ไข
 */
class TbQue extends \yii\db\ActiveRecord
{
    use ModelTrait;

    const STATUS_PRINT = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_que';
    }

    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by','updated_by'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_by'],
                ],
            ],
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at','updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => CoreMultiValueBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'que_num',
                ],
                'value' => function ($event) {
                    if(empty($this->que_num)){
                        return $this->generateQnumber();
                    }else{
                        return $event->sender[$event->data];
                    }
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_id', 'service_group_id', 'que_status'], 'required'],
            [['service_id', 'service_group_id', 'que_status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at','payment_at'], 'safe'],
            [['que_num', 'que_vn', 'que_hn'], 'string', 'max' => 50],
            [['pt_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'que_ids' => 'running',
            'que_num' => 'หมายเลขคิว',
            'que_vn' => 'Visit number ของผู้ป่วย',
            'que_hn' => 'หมายเลข HN ผู้ป่วย',
            'pt_name' => 'ชื่อผู้ป่วย',
            'service_id' => 'ประเภทบริการ',
            'service_group_id' => 'กลุ่มบริการ',
            'que_status' => 'สถานะ',
            'created_at' => 'วันที่บันทึก',
            'updated_at' => 'วันที่แก้ไข',
            'created_by' => 'ผู้บันทึก',
            'updated_by' => 'ผู้แก้ไข',
            'payment_at' => 'เวลาชำระเงินเสร็จ'
        ];
    }

    public function generateQnumber(){
        $service = $this->findModelService($this->service_id);
        $queue = ArrayHelper::map($this->find()->where(['service_id' => $this->service_id])->all(),'que_ids','que_num');
        $qnums = [];
        $maxqnum = null;
        $qid = null;
        if(count($queue) > 0){
            foreach($queue as $key => $q){
                $qnums[$key] = preg_replace("/[^0-9\.]/", '', $q);
            }
            $maxqnum = max($qnums);
            $qid = array_search($maxqnum, $qnums);
        }
        $component = \Yii::createObject([
            'class'     => \common\components\AutoNumber::className(),
            'prefix'    => $service ? $service['service_prefix'] : 'A',
            'number'    => ArrayHelper::getValue($queue,$qid,null),
            'digit'     => $service ? $service['service_numdigit'] : 3,
        ]);
        return $component->generate();
    }

    public function getQueStatus()
    {
        return $this->hasOne(TbQueStatus::className(), ['que_status_id' => 'que_status']);
    }
}
