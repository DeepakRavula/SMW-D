<?php

use yii\helpers\Url;
use yii\helpers\Json;
use common\models\Enrolment;
use kartik\grid\GridView;
use common\models\Lesson;

?>



<div class="private-lesson-index">
<div class="pull-right m-r-10">
    	<a href="#"  title="Add" id="new-lesson" class="add-new-lesson text-add-new"><i class="fa fa-plus"></i></a>
    </div>
    <?php $columns = [
            [
                'label' => 'Due Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->dueDate);
                },
                'group' => true,
            ],
            [
                'label' => 'Program Name',
                'value' => function ($data) {
                    return !empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
                },
            ],
            [
                'label' => 'Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date) . ' @ ' . Yii::$app->formatter->asTime($data->date);
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
                'label' => 'Status',
                'value' => function ($data) {
                    return $data->getStatus();
                },
            ],
            [
                'label' => 'Price',
                'attribute' => 'price',
                'contentOptions' => ['class' => 'text-right'],
                'headerOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    return Yii::$app->formatter->asCurrency($data->privateLesson->total);
                },
            ],
            [
                'label' => 'Owing',
                'attribute' => 'owing',
                'contentOptions' => ['class' => 'text-right'],
                'headerOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    return Yii::$app->formatter->asCurrency($data->privateLesson->balance);
                },
            ],
        ];

    ?>
    <div class="grid-row-open">
        <?php yii\widgets\Pjax::begin(['id' => 'lesson-index', 'timeout' => 6000,]); ?>
    <?php echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'options' => ['id' => 'student-lesson-grid'],
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
    <div class="more-lesson show-more-link" id = "admin-login" style = "display:none">
            <a class = "see-more" href = "">Show More</a>
    </div>
    </div>
</div>

<script>
    $(document).on('depdrop.afterChange', '#lesson-teacher', function() {
        var programs = <?php echo Json::encode($allEnrolments); ?>;
        var selectedProgram = $('#lesson-program').val();
        $.each(programs, function( index, value ) {
            if (value.programId == selectedProgram) {
                $('#lesson-teacher').val(value.teacherId).trigger('change.select2');
            }
        });
        return false;
    });

    $(document).on('click', '#new-lesson', function () {
        $.ajax({
            url    : '<?= Url::to(['extra-lesson/create-private', 'studentId' => $model->id]); ?>',
            type   : 'get',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Add Lesson</h4>');
                    $('#popup-modal .modal-dialog').css({'width': '1000px'});
                }
            }
        });
        return false;
    });

    $(document).ready(function () {
        var lesson_count = '<?= $lessonCount; ?>';
        if (lesson_count > 12) {
                $(".more-lesson").show();
                var url = '<?= Url::to(['lesson/index', 'LessonSearch[studentId]' => $model->id, 'LessonSearch[student]' => $model->fullName, 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON, 'LessonSearch[isSeeMore]' => true]); ?>';
                $('.see-more').attr("href", url);
        } else {
            $(".more-lesson").hide();
        }
    });
</script>

