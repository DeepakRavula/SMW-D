<?php

use backend\models\search\LessonSearch;
use common\components\gridView\KartikGridView;
use common\models\Lesson;
use kartik\daterange\DateRangePicker;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div id="loader" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>
<div class="grid-row-open p-10">
    <?php Pjax::begin(['id' => 'lesson-index', 'timeout' => 25000]);?>
    <?php $columns = [
    [
        'class' => '\kartik\grid\CheckboxColumn',
        'mergeHeader' => false,
    ],
    [
        'label' => 'Date',
        'attribute' => 'dateRange',
        'filter' => '<div class="input-group drp-container">' . DateRangePicker::widget([
            'model' => $searchModel,
            'convertFormat' => true,
            'initRangeExpr' => true,
            'attribute' => 'dateRange',
            'options' => [
                'class' => 'form-control',
                'readOnly' => true,
            ],
            'pluginOptions' => [
                'autoApply' => true,
                'ranges' => [
                    Yii::t('kvdrp', 'Today') => ["moment().startOf('day')", "moment()"],
                    Yii::t('kvdrp', 'Tomorrow') => ["moment().startOf('day').add(1,'days')", "moment().endOf('day').add(1,'days')"],
                    Yii::t('kvdrp', 'Next {n} Days', ['n' => 7]) => ["moment().startOf('day')", "moment().endOf('day').add(6, 'days')"],
                    Yii::t('kvdrp', 'Next {n} Days', ['n' => 30]) => ["moment().startOf('day')", "moment().endOf('day').add(29, 'days')"],
                ],
                'locale' => [
                    'format' => 'M d, Y',
                ],
                'opens' => 'left',
            ],
        ]) . '<span class="input-group-addon remove-button" title="Clear field"><span class="glyphicon glyphicon-remove" ></span></span></div>',
        'value' => function ($data) {
            $date = Yii::$app->formatter->asDate($data->date);
            $lessonTime = (new \DateTime($data->date))->format('H:i:s');

            return !empty($date) ? $date . ' @ ' . Yii::$app->formatter->asTime($lessonTime) : null;
        },
    ],
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
            return $data->course->program->name;
        },
    ],
    [
        'label' => 'Teacher',
        'attribute' => 'teacher',
        'value' => function ($data) {
            return $data->teacher->publicIdentity;
        },
    ],
    [
        'label' => 'Duration',
        'attribute' => 'duration',
        'value' => function ($data) {
            $lessonDuration = (new \DateTime($data->duration))->format('H:i');
            return $lessonDuration;
        },
    ],     
];
if ($searchModel->showAll) {
    array_push($columns, [
        'label' => 'Status',
        'attribute' => 'lessonStatus',
        'filter' => LessonSearch::lessonStatuses(),
        'filterWidgetOptions' => [
            'options' => [
                'id' => 'lesson-index-status',
            ],
        ],
        'value' => function ($data) {
            $status = null;
            if (!empty($data->status)) {
                return $data->getStatus();
            }
            return $status;
        },
    ]);
}
array_push($columns,
     [
        'label' => 'Payment',
        'attribute' => 'owing',
        'attribute' => 'owingStatus',
        'filter'=> LessonSearch::owingStatuses(),
        'contentOptions' => function ($data) {
            $highLightClass = 'text-right';
            if ($data->hasInvoice()) {
                if ($data->invoice->isOwing()) {
                    $highLightClass .= ' danger';
                }
            } else if ($data->privateLesson->balance > 0.09) {
                $highLightClass .= ' danger';
            }
            return ['class' => $highLightClass];
        },
        'headerOptions' => ['class' => 'text-right', 'style' => 'width:10%'],
        'value' => function ($data) {
            if ($data->hasInvoice()) {
                $owingAmount = $data->invoice->balance > 0.09 ? 'owing' : 'paid';
            } else {
                $owingAmount = $data->privateLesson->balance > 0.09 ? 'owing' : 'paid';
            }
            return $owingAmount;
        },
    ]
);

if ((int) $searchModel->type === Lesson::TYPE_GROUP_LESSON) {
    array_shift($columns);
}
?>
    <div class="box">
    <?=KartikGridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['id' => 'lesson-index-1'],
    'filterModel' => $searchModel,
    'summary' => "Showing {begin} - {end} of {totalCount} items",
    'filterUrl' => Url::to(['lesson/index-new', 'LessonSearch[type]' => true, 'LessonSearch[showAll]' => $searchModel->showAll]),
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['lesson/view', 'id' => $model->id]);

        return ['data-url' => $url];
    },
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => $columns,
    'toolbar' => [
        ['content' =>  $this->render('_show-all-button-new', ['searchModel' => $searchModel]),
            'options' => ['title' =>'Filter',]
        ],
        '{export}',
        '{toggleData}'
    ],
    'export' => [
        'fontAwesome' => true,
    ],
    'panel' => [
        'type' => GridView::TYPE_DEFAULT,
        'heading' => 'Private Lessons'
    ],
]);?>
	</div>
	<?php Pjax::end();?>

<?php Modal::begin([
    'header' => '<h4 class="m-0">Substitute Teacher</h4>',
    'id' => 'teacher-substitute-modal',
]);?>
<div id="teacher-substitute-content"></div>
<?php Modal::end();?>
</div>

<script>
      $(document).ready(function () {
        var showAll = $('#lessonsearch1-showall').is(":checked");
        if(showAll == true){
        var student = $("input[name*='LessonSearch1[student]").val();
        var program = $("input[name*='LessonSearch1[program]").val();
        var teacher = $("input[name*='LessonSearch1[teacher]").val();
        var dateRange = $("input[name*='LessonSearch1[dateRange]").val();
        var params = $.param({'LessonSearch1[student]':student, 'LessonSearch1[program]':program, 'LessonSearch1[teacher]':teacher, 'LessonSearch1[dateRange]': dateRange, 'LessonSearch1[type]': <?=Lesson::TYPE_PRIVATE_LESSON?>,'LessonSearch1[showAll]': (showAll | 0), 'LessonSearch1[status]': '' });
        var url = "<?=Url::to(['lesson/index-new']);?>?"+params;
        $.pjax.reload({url: url, container: "#lesson-index", replace: false, timeout: 4000});
        bulkAction.setAction();
        }
    });



    $(document).off('change', '#lesson-index-1 .select-on-check-all, input[name="selection[]"]').on('change', '#lesson-index-1 .select-on-check-all, input[name="selection[]"]', function () {
        bulkAction.setAction();
        return false;
    });

    

  
  

    $(document).on('modal-success', function(event, params) {
        if (!$.isEmptyObject(params.url)) {
            window.location.href = params.url;
        } else if(params.status) {
            $.pjax.reload({container: "#lesson-index-1",timeout: 6000, async:false});
            if (params.message) {
                $('#popup-modal').modal('hide');
                $('#index-success-notification').text(params.message).fadeIn().delay(5000).fadeOut();
            }
        }
        return false;
    });

    $(document).on('modal-error', function(event, params) {
        if (params.error) {
            $('#popup-modal').modal('hide');
            $('#index-error-notification').text(params.error).fadeIn().delay(5000).fadeOut();
        }
        return false;
    });

    $(document).off('change', '#lessonsearch1-showall').on('change', '#lessonsearch1-showall', function(){
        var showAll = $(this).is(":checked");
        var student = $("input[name*='LessonSearch1[student]").val();
        var program = $("input[name*='LessonSearch1[program]").val();
        var teacher = $("input[name*='LessonSearch1[teacher]").val();
        var dateRange = $("input[name*='LessonSearch1[dateRange]").val();
        var params = $.param({'LessonSearch1[student]':student, 'LessonSearch1[program]':program, 'LessonSearch1[teacher]':teacher, 'LessonSearch1[dateRange]': dateRange, 'LessonSearch1[type]': <?=Lesson::TYPE_PRIVATE_LESSON?>,'LessonSearch1[showAll]': (showAll | 0), 'LessonSearch1[status]': '' });
        var url = "<?=Url::to(['lesson/index-new']);?>?"+params;
        $.pjax.reload({url: url, container: "#lesson-index", replace: false, timeout: 4000});  //Reload GridView
    });

    $(document).off('click', '.remove-button').on('click', '.remove-button', function() {
        debugger;
        var dateRange = $("#lessonsearch1-daterange").val();
        if (!$.isEmptyObject(dateRange)) {
            $("#lessonsearch1-daterange").val('').trigger('change');
        }
    });

</script>