<?php
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use inspinia\widgets\Modal;
use inspinia\sweetalert2\assets\SweetAlert2Asset;

$action = Yii::$app->controller->action->id;
SweetAlert2Asset::register($this);

$this->title = 'ตั้งค่า';

echo Tabs::widget([
    'items' => [
        [
            'label' => 'กลุ่มบริการ',
            'options' => ['id' => 'tab-service-group'],
            'url' => Url::to(['/app/setting/service-group']),
            'active' => $action == 'service-group' ? true : false,
        ],
        [
            'label' => 'บัตรคิว',
            'options' => ['id' => 'tab-ticket'],
            'url' => Url::to(['/app/setting/ticket']),
            'active' => $action == 'ticket' ? true : false,
        ],
        [
            'label' => 'จุดบริการ',
            'options' => ['id' => 'tab-counter-service'],
            'url' => Url::to(['/app/setting/counter-service']),
            'active' => $action == 'counter-service' ? true : false,
        ],
        [
            'label' => 'ข้อมูลไฟล์เสียง',
            'options' => ['id' => 'tab-sound'],
            'url' => Url::to(['/app/setting/sound']),
            'active' => $action == 'sound' ? true : false,
        ],
        [
            'label' => 'อัพโหลดไฟล์เสียง',
            'options' => ['id' => 'tab-sound-source'],
            'url' => Url::to(['/app/setting/sound-source']),
            'active' => $action == 'sound-source' ? true : false,
        ],
        [
            'label' => 'โปรแกรมเสียงเรียก',
            'options' => ['id' => 'tab-sound-station'],
            'url' => Url::to(['/app/setting/sound-station']),
            'active' => $action == 'sound-station' ? true : false,
        ],
        [
            'label' => 'เซอร์วิสโปรไฟล์',
            'options' => ['id' => 'tab-service-profile'],
            'url' => Url::to(['/app/setting/service-profile']),
            'active' => $action == 'service-profile' ? true : false,
        ],
        [
            'label' => 'จอแสดงผล',
            'options' => ['id' => 'tab-display'],
            'url' => Url::to(['/app/setting/display']),
            'active' => $action == 'display' ? true : false,
        ],
        [
            'label' => 'รีเช็ตคิว',
            'options' => ['id' => 'tab-reset'],
            'url' => Url::to(['/app/setting/reset-que']),
            'active' => $action == 'reset-que' ? true : false,
        ],
    ],
    'renderTabContent' => false,
    'encodeLabels' => false,
]);

Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",
    'options' => ['class' => 'modal modal-danger','tabindex' => false,],
    'size' => 'modal-lg',
]);

Modal::end();
?>