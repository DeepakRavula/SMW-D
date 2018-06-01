<?php

use yii\helpers\Url;
use common\models\Lesson;
use backend\models\search\LessonSearch;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use common\components\gridView\KartikGridView;
use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Location;
use common\models\Student;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Unscheduled Lessons';
?>
<div class="grid-row-open p-10">
    <?php $columns = [
            [
                'label' => 'Student',
                'value' => function ($data) {
                    return !empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
                },
               
        
            ],
            [
                'label' => 'Program',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
                },
               
            ],
            [
                'label' => 'Teacher',
		        'attribute' => 'teacher',
                'value' => function ($data) {
                    return !empty($data->teacher->publicIdentity) ? $data->teacher->publicIdentity : null;
                },
            ],
            [
                'label' => 'Date',
               
                'value' => function ($data) {
                    $date = Yii::$app->formatter->asDate($data->date);
                    $lessonTime = (new \DateTime($data->date))->format('H:i:s');

                    return !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
                },
            ],
            [
                'label' => 'Status',
                'value' => function ($data) {
                    $status = null;
                    if (!empty($data->status)) {
                        return $data->getStatus();
                    }

                    return $status;
                },
            ],
        ];
     ?>   
    <div class="box">
    <?php echo KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['id' => 'lesson-index-1'],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['lesson/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => $columns,
    ]); ?>
	</div>
