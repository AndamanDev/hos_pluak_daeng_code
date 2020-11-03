<?php

namespace frontend\modules\app\models;

use Yii;

/**
 * This is the model class for table "tb_qtrans_data".
 *
 * @property int $ids
 * @property int $que_trans_ids
 * @property int $que_ids คิวไอดี
 * @property string $created_at วันที่สร้าง
 * @property string $updated_at วันที่แก้ไข
 * @property int $created_by ผู้บันทึก
 * @property int $updated_by ผู้แก้ไข
 * @property int $que_trans_type
 * @property int $que_trans_status สถานะ
 */
class TbQtransData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_qtrans_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['que_trans_ids', 'que_ids', 'created_at', 'created_by'], 'required'],
            [['que_trans_ids', 'que_ids', 'created_by', 'updated_by', 'que_trans_type', 'que_trans_status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ids' => 'Ids',
            'que_trans_ids' => 'Que Trans Ids',
            'que_ids' => 'คิวไอดี',
            'created_at' => 'วันที่สร้าง',
            'updated_at' => 'วันที่แก้ไข',
            'created_by' => 'ผู้บันทึก',
            'updated_by' => 'ผู้แก้ไข',
            'que_trans_type' => 'Que Trans Type',
            'que_trans_status' => 'สถานะ',
        ];
    }
}
