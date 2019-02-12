<?php

use yii\helpers\Html;
use common\models\Location;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Student;
use common\models\UserProfile;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EnrolmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Enrolments';
$this->params['action-button'] = $this->render('_action-menu');

$this->params['show-all'] = $this->render('_button', [
    'searchModel' => $searchModel
]);
?>

<div id="index-success-notification" style="display:none;" class="alert-success alert fade in"></div>
<div id="index-error-notification" style="display:none;" class="alert-danger alert fade in"></div>

<script src="/plugins/bootbox/bootbox.min.js"></script>
<?php $columns = [
    [
        'class' => '\kartik\grid\CheckboxColumn',
        'mergeHeader' => false,
        'contentOptions' => ['style' => 'width:3%'],
    ],
    [
        'attribute' => 'program',
        'label' => 'Program',
        'value' => function ($data) {
            return $data->course->program->name;
        },
        'contentOptions' => ['style' => 'width:17%'],
    ],
    [
        'attribute' => 'student',
        'label' => 'Student',
        'value' => function ($data) {
            return $data->student->fullName;
        },
        'contentOptions' => ['style' => 'width:20%'],
    ],
    [
        'attribute' => 'teacher',
        'label' => 'Teacher',
        'value' => function ($data) {
            
            return $data->course->getTeachers();
        },
        'contentOptions' => ['style' => 'width:20%'],
    ],
    [
        'attribute' => 'startdate',
        'label' => 'Start Date',
        'value' => function ($data) {
            return Yii::$app->formatter->asDate($data->course->startDate);
        },
        'contentOptions' => ['style' => 'width:20%'],
        'filterType' => KartikGridView::FILTER_DATE_RANGE,
        'filterWidgetOptions' => [
            'id' => 'enrolment-startdate-search',
            'convertFormat' => true,
            'initRangeExpr' => true,
            'options' => [
                'readOnly' => true,
            ],
            'pluginOptions' => [
                'autoApply' => true,
                'allowClear' => true,
                'ranges' => [
                    Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["moment().startOf('day').subtract(6, 'days')",
                        'moment()'],
                    Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["moment().startOf('day').subtract(29, 'days')",
                        'moment()'],
                    Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')",
                        "moment().endOf('month')"],
                    Yii::t('kvdrp', 'Last Month') => ["moment().subtract(1, 'month').startOf('month')",
                        "moment().subtract(1, 'month').endOf('month')"],
                ],
                'locale' => [
                    'format' => 'M d, Y',
                ],
                'opens' => 'left'
            ]
        ]
    ],
    [
        'label' => 'End Date',
        'attribute' => 'enddate',
        'contentOptions' => ['style' => 'width:20%'],
        'value' => function ($data) {
            return Yii::$app->formatter->asDate($data->course->endDate);
        },
        'filterType' => KartikGridView::FILTER_DATE_RANGE,
        'filterWidgetOptions' => [
            'id' => 'enrolment-enddate-search',
            'convertFormat' => true,
            'initRangeExpr' => true,
            'options' => [
                'readOnly' => true,
            ],
            'pluginOptions' => [
                'autoApply' => true,
                'allowClear' => true,
                'ranges' => [
                    Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["moment().startOf('day').subtract(6, 'days')",
                        'moment()'],
                    Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["moment().startOf('day').subtract(29, 'days')",
                        'moment()'],
                    Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')",
                        "moment().endOf('month')"],
                    Yii::t('kvdrp', 'Last Month') => ["moment().subtract(1, 'month').startOf('month')",
                        "moment().subtract(1, 'month').endOf('month')"],
                ],
                'locale' => [
                    'format' => 'M d, Y',
                ],
                'opens' => 'left'
            ]
        ]
    ]
]; ?>

<div class="grid-row-open">
    <?= KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['id' => 'enrolment-listing-grid'],
        'summary' => "Showing {begin} - {end} of {totalCount} items",
        'emptyText' => false,
        'toolbar' =>  [
            ['content' =>  Html::a(Yii::t('backend', '<i class="fa fa-plus fa-2x" aria-hidden="true"></i>'), '#',
                ['class' => 'new-enrol-btn'])
            ],
            '{export}',
            '{toggleData}'
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT
        ],
        'toggleDataOptions' => ['minCount' => 20],
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel) {
            $url = Url::to(['enrolment/view', 'id' => $model->id]);
            $data = ['data-url' => $url];
            return $data;
        },
        'columns' => $columns,
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'enrolment-listing'
            ]
        ]
    ]); ?>
</div>

<script>
    $(document).off('click', '#enrolment-teacher-change').on('click', '#enrolment-teacher-change', function(){
        var enrolmentIds = $('#enrolment-listing-grid').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(enrolmentIds)) {
            $('#index-error-notification').html("Choose any enrolments to change teacher").fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ 'EnrolmentSubstituteTeacher[enrolmentIds]': enrolmentIds });
            $.ajax({
                url    : '<?= Url::to(['teacher-substitute/enrolment']) ?>?' + params,
                type   : 'get',
                success: function(response)
                {
                    if (response.status) {
                        $('#modal-content').html(response.data);
                        $('#popup-modal').modal('show');
                    } else {
                        $('#index-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                    }
                }
            });
        }
    });

    $(document).off('change', '#enrolment-listing-grid .select-on-check-all, input[name="selection[]"]').on('change', '#enrolment-listing-grid .select-on-check-all, input[name="selection[]"]', function () {
        bulkAction.setAction();
    });

    var bulkAction = {
        setAction: function() {
            var enrolmentIds = $('#enrolment-listing-grid').yiiGridView('getSelectedRows');
            if ($.isEmptyObject(enrolmentIds)) {
                $('#enrolment-teacher-change').addClass('multiselect-disable');
            } else {
                $('#enrolment-teacher-change').removeClass('multiselect-disable');
            }
            return false;
        }
    };

    $(document).on('click', '.new-enrol-btn', function() {
        $.ajax({
            url    : '<?= Url::to(['course/create-enrolment-basic', 'studentId' => null, 'isReverse' => true]); ?>',
            type   : 'get',
            dataType: "json",
            success: function(response)
            {
                if(response.status)
                {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('.modal-save').show();
                    $('.modal-save').text('Next');
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">New Enrolment Basic</h4>');
                    $('#popup-modal .modal-dialog').css({'width': '600px'});
                }
            }
        });
        return false;
    });

    $(document).on('modal-success', function(event, params) {
        if (params.url) {
            window.location.href = params.url;
        }
    });

    $(document).on('change', '#enrolmentsearch-showallenrolments', function(){
        var showAllEnrolments = $(this).is(":checked");
        var program_search = $("input[name*='EnrolmentSearch[program]").val();
        var student_search = $("input[name*='EnrolmentSearch[student]").val();
        var teacher_search = $("input[name*='EnrolmentSearch[teacher]").val();
        var startDate = $("input[name*='EnrolmentSearch[startdate]").val();
        var params = $.param({ 'EnrolmentSearch[startdate]' :startDate, 'EnrolmentSearch[showAllEnrolments]': (showAllEnrolments | 0),
            'EnrolmentSearch[program]':program_search,'EnrolmentSearch[student]':student_search,
            'EnrolmentSearch[teacher]':teacher_search});
        var url = "<?php echo Url::to(['enrolment/index']); ?>?" + params;
        $.pjax.reload({url:url,container:"#enrolment-listing",replace:false,  timeout: 4000});  //Reload GridView
    });
</script>
