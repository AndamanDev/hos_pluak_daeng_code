<?php

namespace frontend\modules\app\models;

use Yii;

/**
 * This is the model class for table "tb_counter_service".
 *
 * @property int $counter_service_id เลขที่บริการ
 * @property string $counter_service_name ชื่อจุดบริการ
 * @property int $counter_service_call_number หมายเลข
 * @property int $counterservice_type_id ประเภทบริการ
 * @property string $service_group_id กลุ่มบริการ
 * @property int $sound_station_id เครื่องเล่นเสียงที่
 * @property int $sound_id หมายเลขเสียงเรียก
 * @property int $sound_service_id เสียงเรียกบริการ
 * @property int $counter_service_order จัดเรียง
 * @property string $counter_service_status สถานะ
 */
class TbCounterService extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_counter_service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['counter_service_name', 'counter_service_call_number', 'sound_service_id'], 'required'],
            [['counter_service_call_number', 'counter_service_type_id', 'sound_station_id', 'sound_id', 'sound_service_id', 'counter_service_order'], 'integer'],
            [['counter_service_name'], 'string', 'max' => 100],
            [['service_group_id'], 'string', 'max' => 20],
            [['counter_service_status'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'counter_service_id' => 'เลขที่บริการ',
            'counter_service_name' => 'ชื่อจุดบริการ',
            'counter_service_call_number' => 'หมายเลข',
            'counter_service_type_id' => 'ประเภทบริการ',
            'service_group_id' => 'กลุ่มบริการ',
            'sound_station_id' => 'เครื่องเล่นเสียงที่',
            'sound_id' => 'เสียงเรียกหมายเลข',
            'sound_service_id' => 'เสียงเรียกบริการ',
            'counter_service_order' => 'จัดเรียง',
            'counter_service_status' => 'สถานะ',
        ];
    }

    public function getCounterServiceType()
    {
        return $this->hasOne(TbCounterServiceType::className(), ['counter_service_type_id' => 'counter_service_type_id']);
    }

    public function getServiceGroup()
    {
        return $this->hasOne(TbServiceGroup::className(), ['service_group_id' => 'service_group_id']);
    }

    public function getSoundStation()
    {
        return $this->hasOne(TbSoundStation::className(), ['sound_station_id' => 'sound_station_id']);
    }

    public function getSound()
    {
        return $this->hasOne(TbSound::className(), ['sound_id' => 'sound_id']);
    }

    public function getSoundService()
    {
        return $this->hasOne(TbSound::className(), ['sound_id' => 'sound_service_id']);
    }
}
