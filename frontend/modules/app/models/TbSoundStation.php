<?php

namespace frontend\modules\app\models;

use Yii;
use inspinia\behaviors\CoreMultiValueBehavior;
use inspinia\utils\CoreUtility;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tb_sound_station".
 *
 * @property int $sound_station_id
 * @property string $sound_station_name ชื่อ
 * @property string $counter_service_id จุดบริการ
 * @property int $sound_station_status สถานะ
 */
class TbSoundStation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_sound_station';
    }

    public function behaviors()
    {
        return [
            [
                'class' => CoreMultiValueBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'counter_service_id',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'counter_service_id',
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
            [['sound_station_name', 'counter_service_id', 'sound_station_status'], 'required'],
            [['sound_station_status'], 'integer'],
            [['counter_service_id'], 'safe'],
            [['sound_station_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sound_station_id' => 'Sound Station ID',
            'sound_station_name' => 'ชื่อ',
            'counter_service_id' => 'จุดบริการ',
            'sound_station_status' => 'สถานะ',
        ];
    }
}
