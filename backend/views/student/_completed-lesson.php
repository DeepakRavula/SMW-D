<?php

use yii\helpers\Url;
use yii\helpers\Json;
use common\models\Enrolment;
use yii\grid\GridView;
use common\models\Lesson;
use yii\helpers\Html;

?>



<div class="completed-lesson-index">
    <?php $columns = [
            [
                'label' => 'Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date) . ' @ ' . Yii::$app->formatter->asTime($data->date);
                },
            ],
            [
                'label' => 'Program',
                'value' => function ($data) {
                    return !empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
                },
            ],
            [
                'label' => 'Teacher',
                'value' => function ($data) {
                    return $data->teacher->publicIdentity;
                },
            ],
            [
                'label' => 'Duration',
                'value' => function ($data) {
                    $lessonDuration = (new \DateTime($data->duration))->format('H:i');
                    return $lessonDuration;
                },
            ],
            [
                'label' => 'Invoice ID',
                'format' => 'raw',
                'value' => function ($data) {
                    return  ($data->hasInvoice()) ? Html::a($data->invoice->getInvoiceNumber(), [ 'invoice/view',
                    'id' => $data->invoice->id ], ['target' => '_blank']) : "";
                },
            ],
        ];

    ?>
    <div class="grid-row-open">
        <?php yii\widgets\Pjax::begin(['id' => 'completed-lesson-index', 'timeout' => 6000,]); ?>
    <?php echo GridView::widget([
        'dataProvider' => $completedLessonDataProvider,
        'options' => ['id' => 'student-completed-lesson-grid'],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['lesson/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'emptyText' => false,
        'columns' => $columns,
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>
    </div>
</div>