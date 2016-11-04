<?php

use yii\data\ActiveDataProvider;
use common\models\Vacation;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Vacations</h4>
	<a href="#" class="add-new-vacation text-add-new"><i class="fa fa-plus"></i></a>
	<div class="clearfix"></div>
</div>
<div class="dn vacation-create section-tab">
	<?php
	echo $this->render('_form-vacation', [
		'model' => new Vacation(),
		'studentModel' => $studentModel,
	])
	?>

</div>

<?php
$vacations = Vacation::find()
	->where([
	'studentId' => $studentModel->id,
	'isConfirmed' => true,
]);
$vacationDataProvider = new ActiveDataProvider([
	'query' => $vacations,
]);
?>
<div class="grid-row-open">
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
    ],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
</div>