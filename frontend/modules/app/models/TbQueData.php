<?php

namespace frontend\modules\app\models;

use Yii;

/**
 * This is the model class for table "tb_que_data".
 *
 * @property int $ids
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
 * @property string $payment_at เวลาชำระเงินเสร็จ
 */
class TbQueData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_que_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['que_ids', 'que_num', 'service_id', 'service_group_id', 'que_status', 'created_at', 'created_by'], 'required'],
            [['que_ids', 'service_id', 'service_group_id', 'que_status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at', 'payment_at'], 'safe'],
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
            'ids' => 'Ids',
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
            'payment_at' => 'เวลาชำระเงินเสร็จ',
        ];
    }
}
