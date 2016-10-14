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

<div class="grid-row-open p-10">
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php $columns = [
			[
				'label' => 'Student Name',
				'value' => function($data) {
					return ! empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
					}
			],
			[
				'label' => 'Program Name',
				'value' => function($data) {
					return ! empty($data->course->program->name) ? $data->course->program->name : null;
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
				'label' => 'Time',
				'value' => function($data) {
					$lessonTime = (new \DateTime($data->date))->format('H:i:s');
					return Yii::$app->formatter->asTime($lessonTime);
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
        ];
            
        if((int) $searchModel->type ===  Lesson::TYPE_GROUP_LESSON) {
            array_shift($columns);            
        }
     ?>   
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
	'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['lesson/view', 'id' => $model->id]);
        return ['data-url' => $url];
        },
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => $columns,
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>

</div>

