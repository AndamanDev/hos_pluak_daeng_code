<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\icons\Icon;
?>
<div class="row border-bottom">
    <nav class="navbar navbar-fixed-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="javascript:">
                <i class="fa fa-bars"></i>
            </a>
            <div class="app-name" style="margin-top: 18px;padding: 0;float: left;">
                <span class="text welcome-message" style="font-size: 16px;font-weight: 600;"><?= Yii::$app->name; ?></span>
            </div>
        </div>
        <ul class="nav navbar-top-links navbar-right">
            <?php if(!Yii::$app->user->isGuest): ?>
            <li class="dropdown">
                <?= Html::a(Icon::show('dashboard',['style'=>'font-size: 1.5em;']).' <span class="header-menu">แดชบอร์ด</span>',['/'],['title' => 'แดชบอร์ด']); ?>
            </li>
            <li class="dropdown">
                <?= Html::a(Icon::show('desktop',['style'=>'font-size: 1.5em;']).' <span class="header-menu">จอแสดงผล</span>',['/app/display/index'],['title' => 'จอแสดงผล']); ?>
            </li>
            <li class="dropdown">
                <?= Html::a(Icon::show('bar-chart-o',['style'=>'font-size: 1.5em;']).' <span class="header-menu">รายงาน</span>',['/app/report/index'],['title' => 'รายงาน']); ?>
            </li>
            <?php if(\Yii::$app->user->can('Admin')): ?>
            <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="true" title="ตั้งค่าระบบคิว">
                    <i class="fa fa-cogs" style="font-size: 1.5em;"></i> <span class="header-menu">ตั้งค่า</span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu dropdown-alerts">
                    <li>
                        <a href="<?= Url::to(['/app/setting/service-group']) ?>">
                            <div>
                                <i class="fa fa-circle-thin fa-fw" style="font-size: 1.5em;"></i> ระบบคิว
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="<?= Url::to(['/user/admin/index']) ?>">
                            <div>
                                <i class="fa fa-users fa-fw" style="font-size: 1.5em;"></i> ผู้ใช้งาน
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="<?= Url::to(['/rbac']) ?>">
                            <div>
                                <i class="fa fa-circle-thin fa-fw" style="font-size: 1.5em;"></i> สิทธิ์การใช้งาน
                            </div>
                        </a>
                    </li>
                </ul>
            </li>
            <?php endif; ?>
            <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="true" title="เรียกคิว">
                    <i class="fa fa-volume-up" style="font-size: 1.5em;"></i> <span class="header-menu">เรียกคิว</span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu dropdown-alerts">
                    <li>
                        <a href="<?= Url::to(['/app/calling/payment']) ?>">
                            <div>
                                <i class="fa fa-circle-thin fa-fw" style="font-size: 1.5em;"></i> เรียกคิวการเงิน
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="<?= Url::to(['/app/calling/recive-drug']) ?>">
                            <div>
                                <i class="fa fa-circle-thin fa-fw" style="font-size: 1.5em;"></i> เรียกคิวรับยา
                            </div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <?= Html::a(Icon::show('bullhorn',['style'=>'font-size: 1.5em;']).' <span class="header-menu">โปรแกรมเสียง</span>',['/app/calling/player'],['title' => 'โปรแกรมเสียง']); ?>
            </li>
            <li>
                <?= Html::a(Icon::show('sign-out',['style'=>'font-size: 1.5em;']).' <span class="header-menu">ออกจากระบบ</span>',['/auth/logout'],['data-method' => 'post','title' => 'ออกจากระบบ']); ?>
            </li>
        <?php else: ?>
            <li class="dropdown">
                <?= Html::a(Icon::show('dashboard',['style'=>'font-size: 1.5em;']).' <span class="header-menu">แดชบอร์ด</span>',['/'],['title' => 'แดชบอร์ด']); ?>
            </li>
            <li class="dropdown">
                <?= Html::a(Icon::show('desktop',['style'=>'font-size: 1.5em;']).' <span class="header-menu">จอแสดงผล</span>',['/app/display/index'],['title' => 'จอแสดงผล']); ?>
            </li>
            <li class="dropdown">
                <?= Html::a(Icon::show('bullhorn',['style'=>'font-size: 1.5em;']).' <span class="header-menu">โปรแกรมเสียง</span>',['/app/calling/player'],['title' => 'โปรแกรมเสียง']); ?>
            </li>
            <li>
                <?= Html::a(Icon::show('sign-in',['style'=>'font-size: 1.5em;']).' <span class="header-menu">เข้าสู่ระบบ</span>',['/auth/login'],['title' => 'เข้าสู่ระบบ']); ?>
            </li>
        <?php endif; ?>
        </ul>

    </nav>
</div>