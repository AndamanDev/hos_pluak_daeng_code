<?php

namespace frontend\modules\app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "tb_caller".
 *
 * @property int $caller_ids running
 * @property int $que_ids รหัสคิว
 * @property int $que_trans_ids
 * @property int $service_profile_id เซอร์วิสโปรไฟล์
 * @property int $counter_service_id เคาท์เตอร์
 * @property string $call_timestp เวลาเรียก
 * @property int $created_by ผู้เรียก
 * @property string $created_at เวลาบันทึก
 * @property int $updated_by ผู้แก้ไข
 * @property string $updated_at เวลาแก้ไข
 * @property int $call_status สถานะ
 */
class TbCaller extends \yii\db\ActiveRecord
{
    const STATUS_CALLING = 1;
    const STATUS_HOLD = 2;
    const STATUS_CALLEND = 3;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_caller';
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['que_ids', 'que_trans_ids', 'service_profile_id', 'counter_service_id', 'call_timestp', 'call_status'], 'required'],
            [['que_ids', 'que_trans_ids', 'service_profile_id', 'counter_service_id', 'created_by', 'updated_by', 'call_status'], 'integer'],
            [['call_timestp', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'caller_ids' => 'running',
            'que_ids' => 'รหัสคิว',
            'que_trans_ids' => 'Que Trans Ids',
            'service_profile_id' => 'เซอร์วิสโปรไฟล์',
            'counter_service_id' => 'เคาท์เตอร์',
            'call_timestp' => 'เวลาเรียก',
            'created_by' => 'ผู้เรียก',
            'created_at' => 'เวลาบันทึก',
            'updated_by' => 'ผู้แก้ไข',
            'updated_at' => 'เวลาแก้ไข',
            'call_status' => 'สถานะ',
        ];
    }

    public function getQue()
    {
        return $this->hasOne(TbQue::className(), ['que_ids' => 'que_ids']);
    }

    public function getQtrans()
    {
        return $this->hasOne(TbQtrans::className(), ['que_trans_ids' => 'que_trans_ids']);
    }

    public function getServiceProfile()
    {
        return $this->hasOne(TbServiceProfile::className(), ['service_profile_id' => 'service_profile_id']);
    }

    public function getCounterService()
    {
        return $this->hasOne(TbCounterService::className(), ['counter_service_id' => 'counter_service_id']);
    }
}
