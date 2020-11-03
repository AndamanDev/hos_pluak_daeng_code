<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use inspinia\widgets\Modal;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\KeyStorageItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('yii', 'Key Storage Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="key-storage-item-index">
<?php Pjax::begin(['id' => 'pjax-index']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
            'class' => 'grid-view table-responsive'
        ],
        'pjax' => true,
        'responsive' => false,
        'panel' => [
            'heading'=>'<h3 class="panel-title">'.$this->title.'</h3>',
            'type'=>'success',
            'before'=>Html::a('<i class="fa fa-plus"></i> Create Key Storage Item', ['create'], ['class' => 'btn btn-success btn-outline','data-pjax' => 0,'role' => 'modal-remote']),
            'after'=>'',
            'footer'=>false
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'key',
            'value',
            'comment',
            [
                'class' => '\kartik\grid\ActionColumn',
                'template'=>'{update} {delete}',
                'deleteOptions' => [
                    'class' => 'text-danger'
                ],
                'updateOptions' => [
                    'role' => 'modal-remote'
                ],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>
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