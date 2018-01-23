<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="user-index">
<?php
yii\widgets\Pjax::begin([
    'id' => 'user-log',
    'timeout' => 6000,
])
?>
    <?php
    echo GridView::widget([
        'dataProvider' => $logDataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'options' => ['id' => 'log-listing-user'],
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
    ]);
    ?>
<?php \yii\widgets\Pjax::end(); ?>
</div>
