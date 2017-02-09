<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use common\models\ClassroomUnavailability;

$this->title = $model->name;
?>
<div id="classroom-unavailability" class="col-md-12">
	<h4 class="pull-left m-r-20">Unavailabilities</h4>
	<a href="#" class="text-add-new"><i class="fa fa-plus"></i></a>
	<div class="clearfix"></div>
</div>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Unavailability</h4>',
    'id'=>'classroom-unavailability-modal',
]);
 echo $this->render('_form', [
		'model' => new ClassroomUnavailability(),
        'classroomModel' => $model,
]);
Modal::end();
?>
<?php yii\widgets\Pjax::begin([
	'id' => 'classroom-unavailability-grid'
]) ?>
<?php
echo GridView::widget([
    'dataProvider' => $unavailabilityDataProvider,
    'options' => ['class' => 'col-md-5'],
    'tableOptions' => ['class' => 'table table-bordered'],
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
