<?php

use yii\grid\GridView;
use yii\helpers\Url;
use common\models\Invoice;
use common\models\Lesson;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
$this->registerJs("
    $('.private-lesson-index td').click(function (e) {
        var id = $(this).closest('tr').data('id');
        if(e.target == this)
            location.href = '" . Url::to(['lesson/view']) . "?id=' + id;
    });

");
?>
<div class="private-lesson-index p-10">
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
		'rowOptions' => function ($model, $key, $index, $grid) {
            return ['data-id' => $model->id];
        },
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
			[
				'label' => 'Student Name',
				'value' => function($data) {
					return ! empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
                },
			],
			[
				'label' => 'Program Name',
				'value' => function($data) {
					return ! empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
                },
			],
			[
				'label' => 'Date',
				'value' => function($data) {
					$date = Yii::$app->formatter->asDate($data->date); 
					return ! empty($date) ? $date : null;
                },
			],
			[
				'label' => 'Status',
				'value' => function($data) {
					$status = null;
					if (!empty($data->status)) {
					return $data->getStatus();
					}
				return $status;
                },
			],
			[
				'label' => 'Invoiced ?',
				'value' => function($data) {
					$status = null;
				if (!empty($data->invoice->status)) {
					$status = 'Yes'; 
				} else {
					$status = 'No';
				}
				return $status;
			},
			],
        ],
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>

</div>

