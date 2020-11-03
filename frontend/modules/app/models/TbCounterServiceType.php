<?php

namespace frontend\modules\app\models;

use Yii;

/**
 * This is the model class for table "tb_counter_service_type".
 *
 * @property int $counterservice_type_id
 * @property string $counterservice_type_name ประเภทบริการ
 */
class TbCounterServiceType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_counter_service_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['counter_service_type_name'], 'required'],
            [['counter_service_type_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'counter_service_type_id' => 'Counterservice Type ID',
            'counter_service_type_name' => 'ประเภทบริการ',
        ];
    }
}
