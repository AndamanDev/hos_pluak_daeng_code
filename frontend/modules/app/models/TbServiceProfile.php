<?php

namespace frontend\modules\app\models;

use Yii;
use inspinia\behaviors\CoreMultiValueBehavior;
use inspinia\utils\CoreUtility;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "tb_service_profile".
 *
 * @property int $service_profile_id
 * @property string $service_profile_name ชื่อโปรไฟล์
 * @property int $counter_service_type_id เคาท์เตอร์
 * @property string $service_id เซอร์วิสบริการ
 * @property int $service_profile_status สถานะ
 */
class TbServiceProfile extends \yii\db\ActiveRecord
{
    public $counter_service_id;

    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_service_profile';
    }

    public function behaviors()
    {
        return [
            [
                'class' => CoreMultiValueBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'service_id',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'service_id',
                ],
                'value' => function ($event) {
                    return CoreUtility::array2String($event->sender[$event->data]);
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
            [['service_profile_name', 'counter_service_type_id', 'service_id', 'service_profile_status'], 'required'],
            [['counter_service_type_id', 'service_profile_status'], 'integer'],
            [['service_id','counter_service_id'], 'safe'],
            [['service_profile_name'], 'string', 'max' => 100],
            [['service_profile_id','counter_service_id'], 'required', 'on' => 'call'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'service_profile_id' => 'โปรไฟล์',
            'service_profile_name' => 'ชื่อโปรไฟล์',
            'counter_service_type_id' => 'เคาท์เตอร์',
            'service_id' => 'เซอร์วิสบริการ',
            'service_profile_status' => 'สถานะ',
            'counter_service_id' => 'จุดบริการ'
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['service_profile_name', 'counter_service_type_id','service_id','service_profile_status'];
        $scenarios['update'] = ['service_profile_name', 'counter_service_type_id','service_id','service_profile_status'];
        $scenarios['call'] = ['service_profile_id', 'counter_service_id','service_id','counter_service_type_id'];
        return $scenarios;
    }
}
