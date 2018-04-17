<?php

use yii\helpers\Html;
use common\models\Location;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Student;
use common\models\UserProfile;
use common\components\gridView\KartikGridView;
use yii\bootstrap\Modal;
use common\models\LocationAvailability;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EnrolmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Enrolments';
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>'), '#', ['class' => 'new-enrol-btn']);

$this->params['show-all'] = $this->render('_button', [
    'searchModel' => $searchModel
    ]);
?>
<script src="/plugins/bootbox/bootbox.min.js"></script>
	<?php $columns = [
    [
        'attribute' => 'program',
        'label' => 'Program',
        'value' => function ($data) {
            return $data->course->program->name;
        },
                'filterType'=>KartikGridView::FILTER_SELECT2,
                'filter'=>ArrayHelper::map(
            Program::find()->orderBy(['name' => SORT_ASC])
                ->joinWith(['course' => function ($query) {
                    $query->joinWith(['enrolment'])
                        ->confirmed()
                        ->location(Location::findOne(['slug' => \Yii::$app->location])->id);
                }])
                ->asArray()->all(),
                    'id',
                    'name'
                ),
                'filterInputOptions'=>['placeholder'=>'Program'],
                'format'=>'raw',
                'filterWidgetOptions'=>[
                    'pluginOptions'=>[
                        'allowClear'=>true,
            ]
        ],
    ],
    [
        'attribute' => 'student',
        'label' => 'Student',
        'value' => function ($data) {
            return $data->student->fullName;
        },
                'filterType'=>KartikGridView::FILTER_SELECT2,
                'filter'=>ArrayHelper::map(Student::find()->orderBy(['first_name' => SORT_ASC])
                ->joinWith(['enrolment' => function ($query) {
                    $query->joinWith(['course' => function ($query) {
                        $query->confirmed()
                                ->location(Location::findOne(['slug' => \Yii::$app->location])->id);
                    }]);
                }])
                ->asArray()->all(), 'id', 'first_name'),
                'filterWidgetOptions'=>[
            'options' => [
                'id' => 'student',
            ],
                    'pluginOptions'=>[
                        'allowClear'=>true,
            ],
        ],
                'filterInputOptions'=>['placeholder'=>'Student'],
                'format'=>'raw'
    ],
    [
        'attribute' => 'teacher',
        'label' => 'Teacher',
        'value' => function ($data) {
            return $data->course->teacher->publicIdentity;
        },
                'filterType'=>KartikGridView::FILTER_SELECT2,
                'filter'=>ArrayHelper::map(UserProfile::find()->orderBy(['firstname' => SORT_ASC])
                ->joinWith(['courses' => function ($query) {
                    $query->joinWith('enrolment')
                        ->confirmed()
                        ->location(Location::findOne(['slug' => \Yii::$app->location])->id);
                }])
                ->asArray()->all(), 'user_id', 'firstname'),
                'filterWidgetOptions'=>[
            'options' => [
                'id' => 'teacher',
            ],
                    'pluginOptions'=>[
                        'allowClear'=>true,
            ],

        ],
                'filterInputOptions'=>['placeholder'=>'Teacher'],
                'format'=>'raw'
    ],
    [
        'attribute' => 'startdate',
        'label' => 'Start Date',
        'value' => function ($data) {
            return Yii::$app->formatter->asDate($data->course->startDate);
        },
        'contentOptions' => ['style' => 'width:200px'],
        'filterType' => KartikGridView::FILTER_DATE_RANGE,
        'filterWidgetOptions' => [
            'id' => 'enrolment-startdate-search',
            'convertFormat' => true,
            'initRangeExpr' => true,
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
                    'format' => 'M d,Y',
                ],
                'opens' => 'left',
            ],

        ],
    ],

    ]; ?>
<div class="grid-row-open">
<?php
echo KartikGridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false,
    'emptyText' => false,
    'filterModel'=>$searchModel,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
     'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel) {
        $url = Url::to(['enrolment/view', 'id' => $model->id]);
        $data = ['data-url' => $url];
        return $data;
    },
    'columns' => $columns,
        'pjax'=>true,
    'pjaxSettings' => [
        'neverTimeout' => true,
        'options' => [
            'id' => 'enrolment-listing',
        ],
    ],
]);
?>
</div>
<?php Modal::begin([
    'header' => '<h4 class="m-0">New Enrolment</h4>',
    'id' => 'reverse-enrol-modal',
]); ?>
<?= $this->render('_index');?>
<?php Modal::end(); ?>

<script>
    $(document).on('click', '.step1-next', function() {
            if($('#course-programid').val() == "") {
            $('#new-enrolment-form').yiiActiveForm('updateAttribute', 'course-programid', ["Program cannot be blank"]);
        } else {
            $('#step-1, #step-3, #step-4').hide();
            $('#reverse-enrol-modal .modal-dialog').css({'width': '1000px'});
            $('#step-2').show();
            var options = {
                'renderId' : '#reverse-enrolment-calendar',
                'eventUrl' : '<?= Url::to(['teacher-availability/show-lesson-event']) ?>',
                'availabilityUrl' : '<?= Url::to(['teacher-availability/availability']) ?>',
                'changeId' : '#course-teacherid',
                'durationId' : '#courseschedule-duration'
            };
            $.fn.calendarDayView(options);
        }
        return false;
    });

    $(document).on('week-view-calendar-select', function(event, params) {
        $('#course-startdate').val(moment(params.date, "DD-MM-YYYY h:mm a").format('MMM D, Y hh:mm A')).trigger('change');
        $('#courseschedule-day').val(moment(params.date, "DD-MM-YYYY h:mm a").format('dddd')).trigger('change');
        $('#courseschedule-fromtime').val(moment(params.date, "DD-MM-YYYY h:mm a").format('HH:mm:ss')).trigger('change');
        return false;
    });

    $(document).on('click', '.step2-next', function() {
            if($('#course-teacherid').val() == "") {
            $('#new-enrolment-form').yiiActiveForm('updateAttribute', 'course-teacherid', ["Teacher cannot be blank"]);

            }else if($('#courseschedule-day').val() == "") {
            $('#error-notification').html('Please choose the date/time in the calendar').fadeIn().delay(3000).fadeOut();
        } else {
            $('#step-1, #step-2, #step-4').hide();
            $('#step-3').show();
            $('#reverse-enrol-modal .modal-dialog').css({'width': '600px'});
        }
        return false;
    });

    $(document).on('click', '.step2-back', function() {
        $('#step-3, #step-2, #step-4').hide();
        $('#step-1').show();
        $('#reverse-enrol-modal .modal-dialog').css({'width': '600px'});
        return false;
    });

    $(document).on('click', '.step3-next', function() {
            if($('#userprofile-firstname').val() == "") {
            $('#new-enrolment-form').yiiActiveForm('updateAttribute', 'userprofile-firstname', ["Firstname cannot be blank"]);
            } else if($('#userprofile-lastname').val() == "") {
            $('#new-enrolment-form').yiiActiveForm('updateAttribute', 'userprofile-lastname', ["Lastname cannot be blank"]);
            } else if($('#userphone-number').val() == "") {
            $('#new-enrolment-form').yiiActiveForm('updateAttribute', 'userphone-number', ["Number cannot be blank"]);
            } else if($('#useraddress-address').val() == "") {
            $('#new-enrolment-form').yiiActiveForm('updateAttribute', 'useraddress-address', ["Address cannot be blank"]);
        } else {
            $('#step-1, #step-2, #step-3').hide();
            $('#step-4').show();
            $('#reverse-enrol-modal .modal-dialog').css({'width': '400px'});
            var lastName = $('#userprofile-lastname').val();
            $('#student-last_name').val(lastName);
        }
        return false;
    });

    $(document).on('click', '.step3-back', function() {
        $('#step-3, #step-1, #step-4').hide();
        loadCalendar();
        $('#step-2').show();
        $('#reverse-enrol-modal .modal-dialog').css({'width': '1000px'});
        return false;
    });

    $(document).on('click', '.step4-back', function() {
        $('#step-2, #step-3, #step-4').hide();
        $('#step-3').show();
        $('#reverse-enrol-modal .modal-dialog').css({'width': '600px'});
        return false;
    });

    $(document).on('click', '.new-enrol-btn', function() {
        $('#step-2,#step-3, #step-4').hide();
        $('#step-1').show();
        $('#reverse-enrol-modal .modal-dialog').css({'width': '600px'});
        $('#reverse-enrol-modal').modal('show');
        return false;
    });

    $(document).on('click', '.new-enrol-cancel', function() {
        $('#reverse-enrol-modal').modal('hide');
        return false;
    });

    $(document).on('beforeSubmit', '#new-enrolment-form', function(){
        $.ajax({
            url    : '<?= Url::to(['enrolment/add']); ?>',
            type   : 'post',
            dataType: "json",
            data: $(this).serialize()
        });
        return false;
    });

    $(document).on('change', '#enrolmentsearch-showallenrolments', function(){
        var showAllEnrolments = $(this).is(":checked");
        var program_search = $("input[name*='EnrolmentSearch[program]").val();
        var student_search = $("input[name*='EnrolmentSearch[student]").val();
        var teacher_search = $("input[name*='EnrolmentSearch[teacher]").val();
        var params = $.param({ 'EnrolmentSearch[showAllEnrolments]': (showAllEnrolments | 0),
            'EnrolmentSearch[program]':program_search,'EnrolmentSearch[student]':student_search,
            'EnrolmentSearch[teacher]':teacher_search});
        var url = "<?php echo Url::to(['user/index']); ?>?"+params;
        $.pjax.reload({url:url,container:"#user-index",replace:false,  timeout: 6000});
        var url = "<?php echo Url::to(['enrolment/index']); ?>?EnrolmentSearch[showAllEnrolments]=" + (showAllEnrolments | 0);
        $.pjax.reload({url:url,container:"#enrolment-listing",replace:false,  timeout: 4000});  //Reload GridView
    });
</script>
