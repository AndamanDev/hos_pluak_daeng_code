<?php

namespace frontend\modules\app\models;

use Yii;
use trntv\filekit\behaviors\UploadBehavior;
/**
 * This is the model class for table "tb_ticket".
 *
 * @property int $ids
 * @property string $hos_name_th ชื่อ รพ. ไทย
 * @property string $hos_name_en ชื่อ รพ. อังกฤษ
 * @property string $template แบบบัตรคิว
 * @property string $default_template ต้นฉบับบัตรคิว
 * @property string $logo_path
 * @property string $logo_base_url
 * @property string $barcode_type รหัสโค้ด
 * @property int $status สถานะการใช้งาน
 */
class TbTicket extends \yii\db\ActiveRecord
{
    public $logo;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_ticket';
    }

    public function behaviors()
    {
        return [
            'logo' => [
                'class' => UploadBehavior::className(),
                'attribute' => 'logo',
                'pathAttribute' => 'logo_path',
                'baseUrlAttribute' => 'logo_base_url'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hos_name_th', 'barcode_type'], 'required'],
            [['template', 'default_template'], 'string'],
            [['status'], 'integer'],
            [['logo'], 'safe'],
            [['hos_name_th', 'hos_name_en', 'logo_path', 'logo_base_url', 'barcode_type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ids' => 'Ids',
            'hos_name_th' => 'ชื่อ รพ. ไทย',
            'hos_name_en' => 'ชื่อ รพ. อังกฤษ',
            'template' => 'แบบบัตรคิว',
            'default_template' => 'ต้นฉบับบัตรคิว',
            'logo_path' => 'Logo Path',
            'logo_base_url' => 'Logo Base Url',
            'barcode_type' => 'รหัสโค้ด',
            'status' => 'สถานะการใช้งาน',
        ];
    }

    /**
     * {@inheritdoc}
     * @return TbTicketQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TbTicketQuery(get_called_class());
    }

    public function getDefaultTemplate()
    {
        return '<center>
            <div class="x_content">
                <div class="row" style="width: 80mm;margin: auto;">

                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 1cm 21px 0px 21px;">
                        <div class="col-xs-12" style="padding: 0;">
                            <img src="/images/logo/pmh-logo.png" alt="" class="center-block" style="width: 100px">
                        </div>
                        <div class="col-xs-12" style="padding: 0;">
                            <h4 class="color" style="margin-top: 0px;margin-bottom: 0px;text-align: center;"><b style="font-weight: bold;">{hos_name_th}</b></h4>
                            <h6 class="color" style="margin-top: 4px;margin-bottom: 0px;text-align: center;"><b>งานบริการผู้ป่วยนอก</b></h6>
                        </div>
                        <div class="col-xs-12" style="padding: 3px 0px 10px 0px;;text-align: left;">
                            <h6 style="margin: 4px 1px;" class="color">
                                <b style="font-size: 14px; font-weight: 600;">HN</b>  :  <b style="font-size: 13px;">{q_hn}</b>
                            </h6>
                            <h6 style="margin: 4px 1px;" class="color">
                                <b style="font-size: 14px; font-weight: 600;">ชื่อ-นามสกุล</b>  :  <b style="font-size: 13px;">{pt_name}</b>
                            </h6>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px 21px 0px 21px;">
                        <div class="col-xs-12" style="padding: 0;">
                            <h1 style="text-align: center;"><b style="font-weight: 600;text-align: center;">{q_num}</b></h1>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px 21px 0px 21px;">
                        <div class="col-xs-6" style="padding: 0;">
                            <h5 style="text-align: center;"><b style="font-weight: 600;">{pt_visit_type}</b></h5>
                        </div>
                        <div class="col-xs-6" style="padding: 0;">
                            <h5 style="text-align: center;"><b style="font-weight: 600;">{sec_name}</b></h5>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 5px 20px 0px 20px;">
                        <div class="col-xs-12" style="text-align: left;padding: 0;">
                            <div class="col-xs-12" style="padding: 4px 0px 3px 0px;border-top: dashed 1px #404040;">
                                <div class="col-xs-12" style="padding: 1px;">
                                    <h6 class="color" style="margin: 0px;"><b>Scan QR Code เพื่อดูสถานะการรอคิว</b></h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px 21px 0px 21px;">
                        <div class="col-xs-6" style="padding: 0;">
                            <div id="qrcode"><img alt="" src="/images/qrcode.png" /></div>
                        </div>
                        <div class="col-xs-6" style="padding: 0;">
                            <div id="bcTarget" style="overflow: auto; padding: 0px; width: 143px;"><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 10px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 3px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 3px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 3px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 4px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 4px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 4px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 3px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 10px"></div><div style="clear:both; width: 100%; background-color: #FFFFFF; color: #000000; text-align: center; font-size: 10px; margin-top: 5px;">1234567890128</div></div>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12 qwaiting" style="padding: 10px 0px 0px 0px;">
                        <h4 class="color qwaiting" style="margin-top: 0px;margin-bottom: 0px;text-align: center;"><b>คิวรอ</b></h4>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12 qwaiting" style="padding: 10px 0px 0px 0px;">
                        <h4 class="color qwaiting" style="margin-top: 0px;margin-bottom: 0px;text-align: center;"><b>{qwaiting}</b></h4>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 10px 0px 0px 0px;">
                        <h4 class="color" style="margin-top: 0px;margin-bottom: 0px;text-align: center;"><b>ขอบคุณที่ใช้บริการ</b></h4>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px 21px 0px 21px;">
                        <div class="col-xs-6" style="padding: 0;text-align: left;">
                            <h6 class="color" style="margin-top: 4px;margin-bottom: 0px;text-align: left;"><b>{time}</b></h6>
                        </div>
                        <div class="col-xs-6" style="padding: 0;text-align: right;">
                            <h6 class="color" style="margin-top: 4px;margin-bottom: 0px;text-align: right;"><b>{user_print}</b></h6>
                        </div>
                    </div>

                </div>
            </div>
        </center>';
    }

    public function getExampleTemplate()
    {
        return '<center>
            <div class="x_content">
                <div class="row" style="width: 80mm;margin: auto;">

                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 1cm 21px 0px 21px;">
                        <div class="col-xs-12" style="padding: 0;">
                            <img src="/images/logo/pmh-logo.png" alt="" class="center-block" style="width: 100px">
                        </div>
                        <div class="col-xs-12" style="padding: 0;">
                            <h4 class="color" style="margin-top: 0px;margin-bottom: 0px;text-align: center;"><b style="font-weight: bold;">โรงพยาบาลพิมาย</b></h4>
                            <h6 class="color" style="margin-top: 4px;margin-bottom: 0px;text-align: center;"><b>งานบริการผู้ป่วยนอก</b></h6>
                        </div>
                        <div class="col-xs-12" style="padding: 3px 0px 10px 0px;;text-align: left;">
                            <h6 style="margin: 4px 1px;" class="color">
                                <b style="font-size: 14px; font-weight: 600;">HN</b>  :  <b style="font-size: 13px;">0000000000</b>
                            </h6>
                            <h6 style="margin: 4px 1px;" class="color">
                                <b style="font-size: 14px; font-weight: 600;">ชื่อ-นามสกุล</b>  :  <b style="font-size: 13px;">Banbung Hospital</b>
                            </h6>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px 21px 0px 21px;">
                        <div class="col-xs-12" style="padding: 0;">
                            <h1 style="text-align: center;"><b style="font-weight: 600;text-align: center;">1541256</b></h1>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px 21px 0px 21px;">
                        <div class="col-xs-6" style="padding: 0;">
                            <h5 style="text-align: center;"><b style="font-weight: 600;">ผู้ป่วยนัดหมาย</b></h5>
                        </div>
                        <div class="col-xs-6" style="padding: 0;">
                            <h5 style="text-align: center;"><b style="font-weight: 600;">แผนกผู้ป่วยนอก</b></h5>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 5px 20px 0px 20px;">
                        <div class="col-xs-12" style="text-align: left;padding: 0;">
                            <div class="col-xs-12" style="padding: 4px 0px 3px 0px;border-top: dashed 1px #404040;">
                                <div class="col-xs-12" style="padding: 1px;">
                                    <h6 class="color" style="margin: 0px;"><b>Scan QR Code เพื่อดูสถานะการรอคิว</b></h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px 21px 0px 21px;">
                        <div class="col-xs-6" style="padding: 0;">
                            <div id="qrcode"><img alt="" src="/images/qrcode.png" /></div>
                        </div>
                        <div class="col-xs-6" style="padding: 0;">
                            <div id="bcTarget" style="overflow: auto; padding: 0px; width: 143px;"><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 10px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 3px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 3px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 3px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 4px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 4px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 4px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 2px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 3px"></div><div style="float: left; font-size: 0px; width:0; border-left: 3px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 1px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 1px"></div><div style="float: left; font-size: 0px; width:0; border-left: 2px solid #000000; height: 50px;"></div><div style="float: left; font-size: 0px; background-color: #FFFFFF; height: 50px; width: 10px"></div><div style="clear:both; width: 100%; background-color: #FFFFFF; color: #000000; text-align: center; font-size: 10px; margin-top: 5px;">1234567890128</div></div>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12 qwaiting" style="padding: 10px 0px 0px 0px;">
                        <h4 class="color qwaiting" style="margin-top: 0px;margin-bottom: 0px;text-align: center;"><b>คิวรอ</b></h4>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12 qwaiting" style="padding: 10px 0px 0px 0px;">
                        <h4 class="color qwaiting" style="margin-top: 0px;margin-bottom: 0px;text-align: center;"><b>{qwaiting}</b></h4>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 10px 0px 0px 0px;">
                        <h4 class="color" style="margin-top: 0px;margin-bottom: 0px;text-align: center;"><b>ขอบคุณที่ใช้บริการ</b></h4>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0px 21px 0px 21px;">
                        <div class="col-xs-6" style="padding: 0;text-align: left;">
                            <h6 class="color" style="margin-top: 4px;margin-bottom: 0px;text-align: left;"><b>07 ก.พ. 61</b></h6>
                        </div>
                        <div class="col-xs-6" style="padding: 0;text-align: right;">
                            <h6 class="color" style="margin-top: 4px;margin-bottom: 0px;text-align: right;"><b>Admin Banbung</b></h6>
                        </div>
                    </div>

                </div>
            </div>
        </center>';
    }

    public function getTicketPreview()
    {
        $y = date('Y') + 543;
        return strtr($this->template, [
            '{hos_name_th}' => $this->hos_name_th,
            '{q_hn}' => '0008962222',
            '{pt_name}' => 'Banbung Hospital',
            '{q_num}' => 'A001',
            '{pt_visit_type}' => 'ผู้ป่วยนัดหมาย',
            '{sec_name}' => 'แผนกห้องยา',
            '{time}' => \Yii::$app->formatter->asDate('now', 'php:d M ' . substr($y, 2)) . ' ' . \Yii::$app->formatter->asDate('now', 'php:H:i') . ' น.',
            '{user_print}' => 'Admin Hospital',
            '/images/logo/pmh-logo.png' => $this->logo_path ? $this->logo_base_url . '/' . $this->logo_path : '/images/logo/logoBBH.png',
        ]);
    }
}
