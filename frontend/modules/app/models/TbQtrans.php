<?php

namespace frontend\modules\app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "tb_qtrans".
 *
 * @property int $que_trans_ids
 * @property int $que_ids คิวไอดี
 * @property string $created_at วันที่สร้าง
 * @property string $updated_at วันที่แก้ไข
 * @property int $created_by ผู้บันทึก
 * @property int $updated_by ผู้แก้ไข
 * @property int $que_trans_status สถานะ
 */
class TbQtrans extends \yii\db\ActiveRecord
{
    const TYPE_PAYMENT = 1; //payment ชำระเงิน
    const TYPE_RECIVE_DRUG = 2; //รับยา
    const TYPE_WAITING_DRUG = 3; //รอยานาน

    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_qtrans';
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
            [['que_ids'], 'required'],
            [['que_ids', 'created_by', 'updated_by','que_trans_type','que_trans_status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'que_trans_ids' => 'Que Trans Ids',
            'que_ids' => 'คิวไอดี',
            'created_at' => 'วันที่สร้าง',
            'updated_at' => 'วันที่แก้ไข',
            'created_by' => 'ผู้บันทึก',
            'updated_by' => 'ผู้แก้ไข',
            'que_trans_type' => '',
            'que_trans_status' => 'สถานะ'
        ];
    }

    public function getQue()
    {
        return $this->hasOne(TbQue::className(), ['que_ids' => 'que_ids']);
    }

    public function getTypeName($key){
        $items = [
            1 => 'ชำระเงิน',
            2 => 'รับยา',
            3 => 'รอยานาน',
        ];
        return ArrayHelper::getValue($items,$key, '');
    }
}
