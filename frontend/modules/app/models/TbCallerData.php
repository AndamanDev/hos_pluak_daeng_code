<?php

namespace frontend\modules\app\models;

use Yii;

/**
 * This is the model class for table "tb_caller_data".
 *
 * @property int $ids
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
class TbCallerData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_caller_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['caller_ids', 'que_ids', 'que_trans_ids', 'service_profile_id', 'counter_service_id', 'call_timestp', 'call_status'], 'required'],
            [['caller_ids', 'que_ids', 'que_trans_ids', 'service_profile_id', 'counter_service_id', 'created_by', 'updated_by', 'call_status'], 'integer'],
            [['call_timestp', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ids' => 'Ids',
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
}
