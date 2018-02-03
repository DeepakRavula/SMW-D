<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

?>
<div class=" p-15">
    <?php yii\widgets\Pjax::begin([
    'id' => 'lesson-index',
    'timeout' => 6000,
]) ?>
<?php echo GridView::widget([
    'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
    
            [
                'label' => 'Student',
                'value' => function ($data) {
                    return !empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
                },
            ],
            [
                'label' => 'Phone',
                'value' => function ($data) {
                    if (!empty($data->course->enrolment->student->customer->primaryPhoneNumber->number)) {
                        $number = $data->course->enrolment->student->customer->primaryPhoneNumber->number;
                    } else {
                        $number = !empty($data->course->enrolment->student->customer->phoneNumber->number) ? $data->course->enrolment->student->customer->phoneNumber->number : null;
                    }
                    return $number;
                },
            ],
            [
                'label' => 'Program',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
                },
            ],
            [
                'label' => 'Duration',
                'headerOptions' => ['style' => 'text-align:right'],
                'contentOptions' => ['style' => 'text-align:right'],
                'value' => function ($data) {
                    return !empty($data->duration) ? (new \DateTime($data->duration))->format('H:i') : null;
                },
            ],
            [
                'label' => 'Original Date',
                'format' => 'raw',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date);
                },
            ],
            [
                'label' => 'Expiry Date',
                'value' => function ($data) {
                    return !empty($data->privateLesson->expiryDate) ? Yii::$app->formatter->asDate($data->privateLesson->expiryDate) : null;
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{edit}',
                'buttons' => [
                    'edit' => function ($url, $model) {
                        return  Html::a('<i class="fa fa-calendar"></i>', '#', [
                            'id' => 'unschedule-calendar-' . $model->id,
                                                        'duration' => $model->duration,
                            'title' => 'Reschedule',
                            'class' => 'unschedule-calendar m-l-20'
                        ]);
                    },
                ],
            ],
        ],

   ]); ?>

	<?php yii\widgets\Pjax::end(); ?>

</div>

<script type="text/javascript">
    $(document).on('click', '.unschedule-calendar', function () {
        var lessonId = $(this).parent().parent().data('key');
        var params = $.param({ id: lessonId });
        lesson.update(params);
    });
</script>