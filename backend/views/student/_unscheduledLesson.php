<?php

use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

?>
<div class="m-b-10 pull-right">
    <div class="btn-group">
        <i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a id="change-program-teacher" href="#">Change Program/Teacher...</a></li>
        </ul>
    </div>
</div>
<div class="private-lesson-index">
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index',
    'timeout' => 6000,]); ?>
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
                'label' => 'Date',
                'value' => function ($data) {
                    $date = Yii::$app->formatter->asDate($data->date);

                    return !empty($date) ? $date : null;
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
			[
                'label' => 'Status',
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 80px;'],
                'value' => function ($data) {
                    return $data->isExploded ? Html::a('<i class="fa fa-code-fork fa-lg"></i>', null) : null;
                }
            ],
        ];

    ?>
    <div class="grid-row-open">
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
	<?php yii\widgets\Pjax::end(); ?>
    </div>
</div>

<?php Modal::begin([
    'header' => '<h4 class="m-0">Change Program Teacher</h4>',
    'id'=>'change-program-teacher-modal'
]);?>
<div id="change-program-teacher-content"></div>
<?php Modal::end(); ?>

<script>
    $(document).on('click', '#change-program-teacher', function(){
        var lessonIds = $('#unschedule-lesson-index').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds)) {
            $('#index-error-notification').html("Choose any lessons").fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ ids: lessonIds });
            $.ajax({
                url    : '<?= Url::to(['course/change']) ?>?' +params,
                type   : 'get',
                success: function(response)
                {
                    if (response.status) {
                        $('#change-program-teacher-modal').modal('show');
                        $('#change-program-teacher-content').html(response.data);
                    }
                }
            });
            return false;
        }
    });
</script>
