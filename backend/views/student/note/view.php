<?php

use common\models\Note;
use yii\grid\GridView;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div id="student-note" class="col-md-12">
	<h4 class="pull-left m-r-20">Notes</h4>
	<a href="#" class="text-add-new"><i class="fa fa-plus"></i></a>
	<div class="clearfix"></div>
</div>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Notes</h4>',
    'id'=>'student-note-modal',
]);
 echo $this->render('_form', [
		'model' => new Note(),
        'studentModel' => $studentModel,
]);
Modal::end();
?>
<div>
<?php yii\widgets\Pjax::begin([
	'id' => 'student-note-listing',
	'timeout' => 6000,
]) ?>
<?php
echo GridView::widget([
    'dataProvider' => $noteDataProvider,
    'options' => ['class' => 'col-md-12'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
		[
			'label' => 'Content',
			'value' => function($data) {
				return !empty($data->content) ? $data->content : null;
			}
		],
		[
			'label' => 'Created User',
			'value' => function($data) {
				return !empty($data->createdUser->publicIdentity) ? $data->createdUser->publicIdentity : null;
			}
		],
		[
			'label' => 'Updated User',
			'value' => function($data) {
				return !empty($data->updatedUser->publicIdentity) ? $data->updatedUser->publicIdentity : null;
			}
		],
		[
			'label' => 'CreatedOn',
			'value' => function($data) {
				return !empty($data->createdOn) ? Yii::$app->formatter->asDate($data->createdOn) : null;
			}
		],
		[
			'label' => 'UpdatedOn',
			'value' => function($data) {
				return !empty($data->updatedOn) ? Yii::$app->formatter->asDate($data->updatedOn) : null;
			}
		],
    ],
]);
?>
</div>
<?php \yii\widgets\Pjax::end(); ?>
