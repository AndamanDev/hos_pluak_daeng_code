<?php

namespace frontend\modules\app\models;

use Yii;

/**
 * This is the model class for table "tb_service_group".
 *
 * @property int $service_group_id รหัสกลุ่มบริการ
 * @property string $service_group_name ชื่อกลุ่มบริการ
 * @property int $service_group_status สถานะ
 */
class TbServiceGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_service_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_group_name', 'service_group_status'], 'required'],
            [['service_group_status'], 'integer'],
            [['service_group_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'service_group_id' => Yii::t('app', 'รหัสกลุ่มบริการ'),
            'service_group_name' => Yii::t('app', 'ชื่อกลุ่มบริการ'),
            'service_group_status' => Yii::t('app', 'สถานะ'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return TbServiceGroupQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TbServiceGroupQuery(get_called_class());
    }
}
