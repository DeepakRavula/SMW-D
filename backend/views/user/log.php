<?php

use yii\grid\GridView;
use Carbon\Carbon;

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
                'label' => 'Message',
                'format' => 'raw',
                'value' => function ($data) {
                    return  'On '.carbon::parse($data->log->createdOn)->toFormattedDateString().', '.'at '.carbon::parse($data->log->createdOn)->format('h:i A').', '.$data->getMessage();
                },
            ],
        ],
    ]);
    ?>
<?php \yii\widgets\Pjax::end(); ?>
</div>
