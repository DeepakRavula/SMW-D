<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use common\models\ClassroomUnavailability;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
?>
<?php Pjax::begin([
    'id' => 'classroom-view'
]); ?>
<?php 
$this->title = $model->name;
$this->params['label'] = $this->render('_title', [
    'model' => $model,
]);
?>
<?php Pjax::end(); ?>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Unavailability</h4>',
    'id' => 'classroom-unavailability-modal',
]);
echo $this->render('_form', [
    'model' => new ClassroomUnavailability(),
    'classroomModel' => $model,
]);
Modal::end();
?>
    <?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => [
            '<i title="Add" class="fa fa-plus classroom-unavailability m-r-10"></i>',
        ],
        'title' => 'Unavailabilites',
        'withBorder' => true,
    ])
    ?>
    <?php
    yii\widgets\Pjax::begin([
        'id' => 'classroom-unavailability-grid'
    ])
    ?>
    <?php
    echo GridView::widget([
        'dataProvider' => $unavailabilityDataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
                [
                'label' => 'From Date',
                'value' => function ($data) {
                    return !empty($data->fromDate) ? Yii::$app->formatter->asDate($data->fromDate) : null;
                },
            ],
                [
                'label' => 'To Date',
                'value' => function ($data) {
                    return !empty($data->toDate) ? Yii::$app->formatter->asDate($data->toDate) : null;
                },
            ],
                [
                'label' => 'Reason',
                'value' => function ($data) {
                    return !empty($data->reason) ? $data->reason : null;
                },
            ],
        ],
    ]);
    ?>
<?php \yii\widgets\Pjax::end(); ?>
<?php LteBox::end() ?>
