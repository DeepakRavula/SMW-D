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
use common\models\UserProfile;
use kartik\grid\GridView;
?>
<div class="grid-row-open p-10">
    <?php Pjax::begin(['id' => 'lesson-index','timeout' => 6000,]); ?>
    <?php $columns = [
            [
                'label' => 'Student',
                'attribute' => 'student',
                'value' => function ($data) {
                    return !empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
                },
            ],
            [
                'label' => 'Program',
                'attribute' => 'program',
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
                }
            ],
            [
                'label' => 'Enrolment',
                'value' => function ($data) {
                    return $data->enrolment->id;
                }
            ],
	        [
                'label' => 'Duration',
                'value' => function ($data) {
                    $lessonDuration = (new \DateTime($data->duration))->format('H:i');
                    return $lessonDuration;
                }
            ]
        ];       
        array_push($columns, 
            [
                'label' => 'Price',
                'attribute' => 'price',
                'contentOptions' => ['class' => 'text-right'],
                'headerOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    return Yii::$app->formatter->asCurrency(round($data->netPrice, 2));
                }
            ],
            [
                'label' => 'Owing',
                'attribute' => 'owing',
                'contentOptions' => function ($data) {
                    $highLightClass = 'text-right';
                    if ($data->hasInvoice()) {
                        if ($data->invoice->isOwing()) {
                            $highLightClass .= ' danger';
                        }
                    } else if ($data->isOwing($data->enrolment->id)) {
                        $highLightClass .= ' danger';
                    }
                    return ['class' => $highLightClass];
                },
                'headerOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    if ($data->hasInvoice()) {
                        $owingAmount = $data->invoice->balance;
                    } else {
                        $owingAmount = $data->getOwingAmount($data->enrolment->id);
                    }
                    return Yii::$app->formatter->asCurrency(round($owingAmount, 2));
                },
            ]
        );
     ?>   
    <div class="box">
    <?= KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['id' => 'new-lesson-index-1'],
        'summary' => "Showing {begin} - {end} of {totalCount} items",
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['enrolment/view', 'id' => $model->enrolment->id]);

            return ['data-url' => $url];
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => $columns,
    ]); ?>
	</div>
	<?php Pjax::end(); ?>