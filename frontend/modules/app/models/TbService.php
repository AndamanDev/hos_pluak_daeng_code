<?php

namespace frontend\modules\app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tb_service".
 *
 * @property int $service_id
 * @property string $service_name ชื่อบริการ
 * @property int $service_group_id รหัสกลุ่มบริการ
 * @property int $print_template_id แบบการพิมพ์บัตรคิว
 * @property int $print_copy_qty จำนวนพิมพ์/ครั้ง
 * @property string $service_prefix ตัวอักษร/ตัวเลข นำหน้าคิว
 * @property int $service_numdigit จำนวนหลักหมายเลขคิว
 * @property string $service_status สถานะคิว
 */
class TbService extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_UNACTIVE = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_name', 'print_template_id', 'service_prefix', 'service_numdigit','print_copy_qty'], 'required'],
            [['service_group_id', 'print_template_id', 'print_copy_qty', 'service_numdigit'], 'integer'],
            [['service_name'], 'string', 'max' => 255],
            [['service_prefix', 'service_status'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'service_id' => Yii::t('app', 'Service ID'),
            'service_name' => Yii::t('app', 'ชื่อบริการ'),
            'service_group_id' => Yii::t('app', 'รหัสกลุ่มบริการ'),
            'print_template_id' => Yii::t('app', 'แบบการพิมพ์บัตรคิว'),
            'print_copy_qty' => Yii::t('app', 'จำนวนพิมพ์/ครั้ง'),
            'service_prefix' => Yii::t('app', 'ตัวอักษร/ตัวเลข นำหน้าคิว'),
            'service_numdigit' => Yii::t('app', 'จำนวนหลักหมายเลขคิว'),
            'service_status' => Yii::t('app', 'สถานะ'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return TbServiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TbServiceQuery(get_called_class());
    }

    public function getServiceGroup()
    {
        return $this->hasOne(TbServiceGroup::className(), ['service_group_id' => 'service_group_id']);
    }

    public static function itemsAlias($key)
    {
        $items = [
            0 => 'ปิดใช้งาน',
            1 => 'เปิดใช้งาน',
        ];
        return ArrayHelper::getValue($items, $key, '');
    }

    public function getStatus($key)
    {
        return self::itemsAlias($key);
    }
}
