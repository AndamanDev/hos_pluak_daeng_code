<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\KeyStorageItem */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Key Storage Item',
]) . ' ' . $model->key;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Key Storage Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="key-storage-item-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
