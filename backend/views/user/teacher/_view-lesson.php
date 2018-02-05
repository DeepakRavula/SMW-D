<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

?>
<?php $this->render('/lesson/_color-code'); ?>
<div class="col-md-12">
	<?php
    $form = ActiveForm::begin([
            'id' => 'teacher-lesson-search-form',
    ]);
    ?>
    <div class="row">
        <div class="col-md-3 form-group">
            <?php
            echo DateRangePicker::widget([
                'model' => $searchModel,
                'attribute' => 'dateRange',
                'convertFormat' => true,
                'initRangeExpr' => true,
                'pluginOptions' => [
                    'autoApply' => true,
                    'ranges' => [
                        Yii::t('kvdrp', 'Today') => ["moment().startOf('day')", "moment()"],
                        Yii::t('kvdrp', 'Tomorrow') => ["moment().startOf('day').add(1,'days')", "moment().endOf('day').add(1,'days')"],
                        Yii::t('kvdrp', 'Next {n} Days', ['n' => 7]) => ["moment().startOf('day')", "moment().endOf('day').add(6, 'days')"],
                        Yii::t('kvdrp', 'Next {n} Days', ['n' => 30]) => ["moment().startOf('day')", "moment().endOf('day').add(29, 'days')"],
                    ],
                    'locale' => [
                        'format' => 'M d,Y',
                    ],
                    'opens' => 'right',
                ],
            ]);

            ?>
        </div>
        <div class="col-md-1 form-group">
            <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['id' => 'lesson-search', 'class' => 'btn btn-primary']) ?>
        </div>
        <div class="col-md-1 form-group">
            <?= Html::a('<i class="fa fa-print"></i> Print', ['print/teacher-lessons', 'id' => $model->id], ['id' => 'print-btn', 'class' => 'btn btn-default m-r-10', 'target' => '_blank']) ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
$columns = [
        [
        'value' => function ($data) {
            if (! empty($data->date)) {
                $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
                return $lessonDate->format('l, F jS, Y');
            }

            return null;
        },
        'group' => true,
        'groupedRow' => true,
        'groupFooter' => function ($model, $key, $index, $widget) {
            return [
                'mergeColumns' => [[1, 3]],
                'content' => [
                    4 => GridView::F_SUM,
                ],
                'contentFormats' => [
                    4 => ['format' => 'number', 'decimals' => 2],
                ],
                'contentOptions' => [
                    4 => ['style' => 'text-align:right'],
                ],
            'options'=>['style'=>'font-weight:bold;']
            ];
        }
    ],
        [
        'label' => 'Time',
        'width' => '250px',
        'value' => function ($data) {
            return !empty($data->date) ? Yii::$app->formatter->asTime($data->date) : null;
        },
    ],
        [
        'label' => 'Program',
        'width' => '250px',
        'value' => function ($data) {
            return !empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
        },
    ],
        [
        'label' => 'Student',
        'value' => function ($data) {
            $student = ' - ';
            if ($data->course->program->isPrivate()) {
                $student = !empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
            }
            return $student;
        },
    ],
        [
        'label' => 'Duration(hrs)',
        'value' => function ($data) {
            return $data->getDuration();
        },
        'contentOptions' => ['class' => 'text-right'],
            'hAlign'=>'right',
            'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
    ],
];
?>
<?=
GridView::widget([
    'dataProvider' => $teacherLessonDataProvider,
        'summary' => false,
        'emptyText' => false,
    'options' => ['class' => 'col-md-12'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'pjax' => true,
    'showPageSummary' => true,
    'pjaxSettings' => [
        'neverTimeout' => true,
        'options' => [
            'id' => 'teacher-lesson-grid',
        ],
    ],
    'columns' => $columns,
]);
?>

<script>
    $(document).on('click', '#teacher-lesson-grid  tbody > tr', function () {
        var lessonId = $(this).data('key');
        var params = $.param({ id: lessonId });
        lesson.update(params);
    });
    
    $("#teacher-lesson-search-form").on("submit", function () {
        var dateRange = $('#lessonsearch-daterange').val();
        $.pjax.reload({container: "#teacher-lesson-grid", replace: false, timeout: 6000, data: $(this).serialize()});
        var params = $.param({ 'LessonSearch[dateRange]': dateRange});
        var url = '<?= Url::to(['print/teacher-lessons', 'id' => $model->id]); ?>&' + params;
        $('#print-btn').attr('href', url);
        return false;
    });
</script>