<?php

namespace frontend\modules\app\models;

use Yii;
use inspinia\behaviors\CoreMultiValueBehavior;
use inspinia\utils\CoreUtility;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tb_display".
 *
 * @property int $display_ids
 * @property string $display_name ชื่อจอแสดงผล
 * @property int $page_length จำนวนแถวที่แสดง
 * @property string $text_th_left
 * @property string $text_th_right
 * @property string $color_th_left
 * @property string $color_th_right
 * @property string $display_css
 * @property int $display_status สถานะ
 */
class TbDisplay extends \yii\db\ActiveRecord
{
    public $color_code;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_display';
    }

    public function behaviors()
    {
        return [
            [
                'class' => CoreMultiValueBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['service_id','counter_service_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['service_id','counter_service_id'],
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
            [['display_name', 'page_length', 'display_status','counter_service_id','service_id','que_column_length'], 'required'],
            [['page_length', 'display_status','que_column_length','show_que_waitdrug'], 'integer'],
            [['que_column_length'], 'integer', 'max' => 5],
            [['display_css'], 'string'],
            [['counter_service_id','service_id'], 'safe'],
            [['display_name', 'text_th_left', 'text_th_right'], 'string', 'max' => 255],
            [['color_th_left', 'color_th_right','background_color','text_hold'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'display_ids' => 'Display Ids',
            'display_name' => 'ชื่อจอแสดงผล',
            'page_length' => 'จำนวนแถวที่แสดง',
            'text_th_left' => 'Text Th Left',
            'text_th_right' => 'Text Th Right',
            'color_th_left' => 'Color Th Left',
            'color_th_right' => 'Color Th Right',
            'display_css' => 'Display Css',
            'background_color' => 'สีพื้นหลัง',
            'display_status' => 'สถานะ',
            'text_hold' => 'ข้อความตารางพักคิว',
            'counter_service_id' => 'เคาท์เตอร์',
            'service_id' => 'ประเภทบริการ',
            'que_column_length' => 'จำนวนเลขคิวที่แสดง/แถว',
            'show_que_waitdrug' => 'แสดงคิวรอยานาน',
        ];
    }

    public function getDefaultCss(){
        return '<pre class="css" data-pbcklang="css" data-pbcktabsize="4">
        /*สีพื้นหลัง*/
        body {
            background-color: #204d74;
        }
        table#tb-display thead tr th.th-right {
            border-top: 5px solid #ffffff !important;
            border-bottom: 5px solid #ffffff !important;
            border-right: 5px solid #ffffff !important;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            /*font-size: 40px !important;*/
        }
        
        table#tb-display thead tr th.th-left {
            border-top: 5px solid #ffffff !important;
            border-bottom: 5px solid #ffffff !important;
            border-left: 5px solid #ffffff !important;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            /*font-size: 40px !important;*/
        }
        
        table#tb-display tbody tr td.td-left {
            border-top: 5px solid #ffffff !important;
            border-bottom: 5px solid #ffffff !important;
            border-left: 5px solid #ffffff !important;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            /*font-size: 40px !important;*/
        }
        
        table#tb-display tbody tr td.td-right {
            border-top: 5px solid #ffffff !important;
            border-bottom: 5px solid #ffffff !important;
            border-right: 5px solid #ffffff !important;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            /*font-size: 40px !important;*/
        }
        /*background-color = สีพื้นหลังตารางส่วนหัว*/
        /*color = สีตัวอักษร*/
        table#tb-display thead tr {
            width: 50%;
            border-radius: 15px;
            border: 5px solid white;
            background-color: #000000;
            color: #ffffff;
            font-weight: bold;
        }
        /*background-color = สีพื้นหลังแถว*/
        /*color = สีตัวอักษร*/
        table#tb-display tbody tr {
            width: 50%;
            border-radius: 15px;
            border: 5px solid white;
            background-color: #666666;
            color: #ffffff;
            font-weight: bold;
        }
        table#tb-hold tbody tr td.td-left{
            width: 50%;
            border-top: 5px solid #ffffff !important;
            border-bottom: 5px solid #ffffff !important;
            border-right: 5px solid #ffffff !important;
            border-left: 5px solid #ffffff !important;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            background-color: #666666;
            color: #ffffff;
            vertical-align: middle;
            /*font-size: 40px !important;*/
        }
        
        table#tb-hold tbody tr td.td-right{
            width: 50%;
            border-top: 0px solid white !important;
            color: yellow;
            font-weight: bold;
            vertical-align: middle;
            /*font-size: 40px !important;*/
        }</pre>';
    }

    public function getServiceData(){
        $query = Yii::$app->db->createCommand('SELECT
                tb_service.service_id,
                CONCAT(tb_service.service_prefix,\': \', tb_service.service_name) as service_name
                FROM
                tb_service
                INNER JOIN tb_service_group ON tb_service_group.service_group_id = tb_service.service_group_id
                WHERE
                tb_service.service_status = 1')->queryAll();
        return ArrayHelper::map($query,'service_id','service_name');
    }

    public function getCounterServiceData(){
        $query = Yii::$app->db->createCommand('SELECT
                tb_counter_service_type.counter_service_type_id,
                tb_counter_service_type.counter_service_type_name
                FROM
                tb_counter_service_type')->queryAll();
        return ArrayHelper::map($query,'counter_service_type_id','counter_service_type_name');
    }
}
