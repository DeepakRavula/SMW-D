<?php

use yii\helpers\Html;
use common\models\Location;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Student;
use common\models\UserProfile;
use common\components\gridView\KartikGridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EnrolmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Enrolments';
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>'), '#',
    ['class' => 'new-enrol-btn']);

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
                ->all(), 'id', 'fullName'),
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
                ->all(), 'user_id', 'fullName'),
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

<script>
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
        var program_search = $("#enrolmentsearch-program").select2("val");
        var student_search = $("#student").val();
        var teacher_search = $("input[name*='EnrolmentSearch[teacher]").val();
        var params = $.param({ 'EnrolmentSearch[showAllEnrolments]': (showAllEnrolments | 0),
            'EnrolmentSearch[program]':program_search,'EnrolmentSearch[student]':student_search,
            'EnrolmentSearch[teacher]':teacher_search});
        var url = "<?php echo Url::to(['enrolment/index']); ?>?" + params;
        $.pjax.reload({url:url,container:"#enrolment-listing",replace:false,  timeout: 4000});  //Reload GridView
    });
</script>
