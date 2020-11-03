<?php
use inspinia\widgets\Modal;

Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",
    'options' => ['class' => 'modal modal-danger','tabindex' => false,],
    'size' => 'modal-lg',
]);

Modal::end();
?>