<?php
use yii\helpers\Html;
use inspinia\widgets\Menu;
$identity = Yii::$app->user->identity;
?>
<nav class="navbar-default navbar-static-side" role="navigation" style="margin-top: 60px">
    <div class="sidebar-collapse">
        <?php
        if(!Yii::$app->user->isGuest){
            echo Menu::widget([
                'items' => [
                    [
                        'label' => '<div class="dropdown profile-element">
                        <span>
                            <img alt="image" class="img-circle img-responsive" width="48px" src="'.$identity->profile->getAvatar().'" />
                        </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">'.$identity->profile->name.'</strong>
                                </span> <span class="text-muted text-xs block">ตัวเลือก <b class="caret"></b></span> </span> </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li>'.Html::a('ข้อมูลส่วนตัว', ['/user/settings/profile'], []).'</li>
                            <li class="divider"></li>
                            <li>'.Html::a('ออกจากระบบ', ['/auth/logout'], ['data-method' => 'post']).'</li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        PMH
                    </div>',
                        'nav-header' => true,
                        'options' => ['class' => 'nav-header'],
                    ],
                    [
                        'label' => 'หน้าหลัก',
                        'icon' => 'dashboard',
                        'url' => ['/site/index'],
                    ],
                    [
                        'label' => 'ออกบัตรคิว',
                        'icon' => 'credit-card',
                        'url' => ['/app/kiosk/ticket'],
                        'visible' => \Yii::$app->user->can('ticket') || \Yii::$app->user->can('Admin')
                    ],
                    [
                        'label' => 'ตรวจสอบยา',
                        'icon' => 'pencil-square-o',
                        'url' => ['/app/calling/check-drug'],
                        'visible' => \Yii::$app->user->can('check-drug') || \Yii::$app->user->can('Admin')
                    ],
                    ['label' => 'เรียกคิว','icon' => 'volume-up', 'url' => false, 'items' => [
                        ['label' => 'การเงิน', 'icon' => 'circle-thin', 'url' => ['/app/calling/payment'],'children' => true,'visible' => \Yii::$app->user->can('payment') || \Yii::$app->user->can('Admin')],
                        ['label' => 'รับยา', 'icon' => 'circle-thin', 'url' => ['/app/calling/recive-drug'],'children' => true,'visible' => \Yii::$app->user->can('recive-drug') || \Yii::$app->user->can('Admin')],
                    ]],
                    [
                        'label' => 'โปรแกรมเสียงเรียก',
                        'icon' => 'bullhorn',
                        'url' => ['/app/calling/player'],
                    ],
                    [
                        'label' => 'จอแสดงผล',
                        'icon' => 'desktop',
                        'url' => ['/app/display/index'],
                    ],
                    [
                        'label' => 'รายงาน',
                        'icon' => 'file-text-o',
                        'url' => ['/app/report/index'],
                    ],
                    [
                        'label' => 'ข้อมูลส่วนตัว',
                        'icon' => 'user',
                        'url' => ['/user/settings/profile'],
                    ],
                    ['label' => 'ตั้งค่า','icon' => 'cogs', 'url' => false, 'items' => [
                        ['label' => 'ระบบคิว', 'icon' => 'circle-thin', 'url' => ['/app/setting/service-group'],'children' => true],
                        ['label' => 'ผู้ใช้งาน', 'icon' => 'users', 'url' => ['/user/admin/index'],'children' => true],
                        ['label' => 'สิทธิ์การใช้งาน', 'icon' => 'circle-thin', 'url' => ['/rbac'],'children' => true],
                        ['label' => 'Key Storage Items', 'icon' => 'circle-thin', 'url' => ['/key-storage/index'],'children' => true],
                    ],'visible' => \Yii::$app->user->can('Admin')],
                    [
                        'label' => 'ออกจากระบบ', 'icon' => 'sign-out', 'url' => ['/auth/logout'],'template' => '<a href="{url}" data-method="post">{icon} <span class="nav-label">{label}</span></a>'
                    ],
                ],
                'options' => [
                    'id' => 'side-menu',
                    'class' => 'nav metismenu',
                ],
                'encodeLabels' => false,
            ]);
        }else{
            echo Menu::widget([
                'items' => [
                    [
                        'label' => 'แดชบอร์ด',
                        'icon' => 'dashboard',
                        'url' => ['/site/index'],
                    ],
                    [
                        'label' => 'โปรแกรมเสียงเรียก',
                        'icon' => 'bullhorn',
                        'url' => ['/app/calling/player'],
                    ],
                    [
                        'label' => 'จอแสดงผล',
                        'icon' => 'desktop',
                        'url' => ['/app/display/index'],
                    ],
                ],
                'options' => [
                    'id' => 'side-menu',
                    'class' => 'nav metismenu',
                ],
                'encodeLabels' => false,
            ]);
        }
        ?>

    </div>
</nav>
