<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use common\models\Lesson;

?>

<?php
$form = ActiveForm::begin([
    'action' => Url::to(['student/view', 'id' => $model->id]),
    'method' => 'post',
'fieldConfig' => [
    'options' => [
        'tag' => false,
    ],
],
    ]);
?>
<?= $form->field($unscheduledLessonSearchModel, 'showAll')->checkbox(['data-pjax' => true]); ?>
<?php ActiveForm::end(); ?>

<div class="m-b-10 pull-right">
    <div class="btn-group">
        <i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a id="change-program-teacher" href="#">Change Program/Teacher...</a></li>
        </ul>
    </div>
</div>
<div class="private-lesson-index">

    <?php $columns = [
            [
                'class' => '\yii\grid\CheckboxColumn',
                'contentOptions' => ['style' => 'width: 30px;'],
            ],
            [
                'label' => 'Program',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
                },
            ],
            [
                'label' => 'Phone',
                'value' => function ($data) {
                    return !empty($data->course->enrolment->student->customer->phoneNumber->number) ? $data->course->enrolment->student->customer->phoneNumber->number : null;
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
                'value' => function ($data) {
                    return $data->rootLesson ? Yii::$app->formatter->asDate($data->getOriginalDate()) : $data->date;
                },
            ],
            [
                'label' => 'Expiry Date',
                'value' => function ($data) {
                    if (!empty($data->privateLesson->expiryDate)) {
                        $date = Yii::$app->formatter->asDate($data->privateLesson->expiryDate);
                    }

                    return !empty($date) ? $date : null;
                },
            ],
        ];

    ?>
    <div class="grid-row-open">
    <?php Pjax::begin(['id' => 'student-view-unschedule-lesson-index', 'timeout' => 6000,]); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['id' => 'unschedule-lesson-index'],
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
	<?php Pjax::end(); ?>
    </div>
</div>

<script>
    $(document).off('click', '#change-program-teacher').on('click', '#change-program-teacher', function(){
        var lessonIds = $('#unschedule-lesson-index').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds)) {
            $('#enrolment-delete').html("Choose any lessons").fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ 'LessonSearch[ids]': lessonIds });
            $.ajax({
                url    : '<?= Url::to(['course/change']) ?>?' +params,
                type   : 'get',
                success: function(response)
                {
                    if (response.status) {
                        $('#popup-modal').modal('show');
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Change Program Teacher</h4>');
                        $('#modal-content').html(response.data);
                    }
                }
            });
            return false;
        }
    });

    $(document).off('change', '#unscheduledlessonsearch-showall').on('change', '#unscheduledlessonsearch-showall', function () {
      	var showAllExpiredLesson = $(this).is(":checked");
    	var params = $.param({ 'UnscheduledLessonSearch[showAll]': (showAllExpiredLesson | 0)});
      	var url = "<?= Url::to(['student/view', 'id' => $model->id]); ?>&"+params;
        $.pjax.reload({url: url, container: "#student-view-unschedule-lesson-index", replace: false, timeout: 4000});  //Reload GridView
});
</script>
