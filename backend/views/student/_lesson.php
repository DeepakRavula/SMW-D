<?php

use yii\helpers\Html;
use common\models\Lesson;
use common\models\Invoice;
use yii\grid\GridView;
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Lessons</h4>
 <a href="#" class="add-new-lesson text-add-new"><i class="fa fa-plus"></i></a>
 <div class="clearfix"></div>
 </div>
 <div class="dn lesson-create section-tab">
     <?php echo $this->render('//lesson/_form', [
         'model' => new Lesson(),
         'studentModel' => $model,

         ]) 
             ?>

</div>
<?php yii\widgets\Pjax::begin([
	'timeout' => 6000,
]) ?>
<?php
echo GridView::widget([
	'dataProvider' => $lessonDataProvider,
	'rowOptions' => function ($model, $key, $index, $grid) {
		$u= \yii\helpers\StringHelper::basename(get_class($model));
		$u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
		return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
	},
	'options' => ['class' => 'col-md-12'],
	'tableOptions' =>['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray' ],
	'columns' => [
		[
			'label' => 'Program Name',
			'value' => function($data) {
				return !empty($data->course->program->name) ? $data->course->program->name : null;
			},
		],
		[
			'label' => 'Time',
			'value' => function($data){
				$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
				$startTime = $lessonDate->format('H:i:s');
					return Yii::$app->formatter->asTime($startTime) . ' to ' . Yii::$app->formatter->asTime($data->toTime);
			}
		],
		[
			'label' => 'Lesson Status',
			'value' => function($data) {
				$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
				$currentDate = new \DateTime();

				if ($lessonDate <= $currentDate) {
					$status = 'Completed';
				} else {
					$status = 'Scheduled';
				}

				return $status;
			},
		],
		[
			'label' => 'Invoice Status',
			'value' => function($data) {
				$status = null;
				if (!empty($data->invoice->status)) {
					return $data->invoice->getStatus(); 
				} else {
					$status = 'Not Invoiced';
				}
				return $status;
			},
		],
		'date:date'
	],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
