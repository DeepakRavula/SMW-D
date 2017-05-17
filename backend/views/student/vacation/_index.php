<?php

use yii\data\ActiveDataProvider;
use common\models\Vacation;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Vacations</h4>
</div>

<?php
$vacations = Vacation::find()
	->joinWith(['enrolment' => function($query) use($studentModel){
		$query->andWhere(['studentId' => $studentModel->id]);	
	}])
	->andWhere(['vacation.isConfirmed' => true]);
$vacationDataProvider = new ActiveDataProvider([
	'query' => $vacations,
]);
?>
<div>
<?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
]) ?>
<?php
echo GridView::widget([
    'dataProvider' => $vacationDataProvider,
    'options' => ['class' => 'col-md-12'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
		[
            'label' => 'Program',
            'value' => function ($data) {
                return  $data->enrolment->course->program->name;

            },
        ],
        [
            'label' => 'From Date',
            'value' => function ($data) {
                return  Yii::$app->formatter->asDate($data->fromDate);

            },
        ],
		[
            'label' => 'To Date',
            'value' => function ($data) {
                return  Yii::$app->formatter->asDate($data->toDate);

            },
        ],
		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{delete}',
			'buttons' => [
				'delete' => function ($url, $model, $key) {
					return Html::a('<i class="fa fa-times" aria-hidden="true"></i>',
							['vacation/delete', 'id' => $model->id],
							[
							'data' => [
								'method' => 'post',
							],
					]);
				},
				],
			],
		],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
</div>