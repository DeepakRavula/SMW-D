<?php

use yii\grid\GridView;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'History',
    'withBorder' => true,
])
?>
<div class="student-index"> 
    <?php yii\widgets\Pjax::begin([
        'id' => 'invoice-log',
        'timeout' => 6000,
    ]) ?>
<?php echo GridView::widget([
    'dataProvider' => $logDataProvider,
    'summary' => false,
    'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
        [
            'label' => 'Createdon',
            'value' => function ($data) {
                return $data->log->createdOn;
            },
            'format' => 'datetime',
        ],
        [
            'label' => 'Message',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getMessage();
            },
        ],
    ],
]); ?>
    <?php \yii\widgets\Pjax::end(); ?>
</div>
<?php LteBox::end() ?>
	