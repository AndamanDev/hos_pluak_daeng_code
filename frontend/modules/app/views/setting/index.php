<?php
use inspinia\widgets\Modal;
use inspinia\sweetalert2\assets\SweetAlert2Asset;

$this->title = 'ตั้งค่า';
$this->params['breadcrumbs'][] = ['label' => 'ตั้งค่า', 'url' => ['/app/setting/index']];
$this->params['breadcrumbs'][] = ['label' => 'ระบบคิว', 'url' => ['/app/setting/index']];

SweetAlert2Asset::register($this);
?>
<div class="tabs-container">
    <?= $this->render('_tabs'); ?>
</div>
<?php
Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",
    'options' => ['class' => 'modal modal-danger','tabindex' => false,],
    'size' => 'modal-lg',
]);

Modal::end();
?>