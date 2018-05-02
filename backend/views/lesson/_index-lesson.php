<?php

use yii\helpers\Url;
use common\models\Lesson;
use backend\models\search\LessonSearch;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Private Lessons';
$this->params['action-button'] = $this->render('_action-menu', [
    'searchModel' => $searchModel
]);
?>

<div class="grid-row-open p-10">
    <?php Pjax::begin(['id' => 'lesson-index','timeout' => 6000,]); ?>
    <?php $columns = [
            [
                'class' => '\kartik\grid\CheckboxColumn',
                'mergeHeader' => false
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
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
                },
            ],
            [
                'label' => 'Teacher',
		        'attribute' => 'teacher',
                'value' => function ($data) {
                    return !empty($data->teacher->publicIdentity) ? $data->teacher->publicIdentity : null;
                },
            ],
            [
                'label' => 'Date',
                'attribute' => 'dateRange',
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'convertFormat' => true,
                    'initRangeExpr' => true,
                    'attribute' => 'dateRange',
                    'convertFormat' => true,
                    'pluginOptions'=>[
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
                        'opens' => 'left'
                    ],
                ]),
                'value' => function ($data) {
                    $date = Yii::$app->formatter->asDate($data->date);
                    $lessonTime = (new \DateTime($data->date))->format('H:i:s');

                    return !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
                },
            ],
            [
                'label' => 'Status',
                'attribute' => 'lessonStatus',
                'filter' => LessonSearch::lessonStatuses(),
                'value' => function ($data) {
                    $status = null;
                    if (!empty($data->status)) {
                        return $data->getStatus();
                    }

                    return $status;
                },
            ],
            [
                'label' => 'Invoiced ?',
                'attribute'=> 'invoiceStatus',
                'filter'=>LessonSearch::invoiceStatuses(),
                'value' => function ($data) {
                    $status = null;
                    if (!empty($data->invoice)) {
                        $status = 'Yes';
                    } else {
                        $status = 'No';
                    }

                    return $status;
                },
            ],
              [
                'label' => 'Present',
                'attribute' => 'attendanceStatus',
                'filter' => LessonSearch::attendanceStatuses(),
                'value' => function ($data) {
                    $status = null;
                    if (!empty($data->isPresent)) {
                        return $data->getPresent();
                    }

                    return $status;
                },
            ],
        ];

        if ((int) $searchModel->type === Lesson::TYPE_GROUP_LESSON) {
            array_shift($columns);
        }
     ?>   
    <div class="box">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'options' => ['id' => 'lesson-index-1'],
        'filterModel' => $searchModel,
        'filterUrl' => Url::to(['lesson/index', 'LessonSearch[type]' => true]),
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['lesson/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => $columns,
    ]); ?>
	</div>
	<?php Pjax::end(); ?>

<?php Modal::begin([
        'header' => '<h4 class="m-0">Substitute Teacher</h4>',
        'id'=>'teacher-substitute-modal',
]);?>
<div id="teacher-substitute-content"></div>
<?php Modal::end(); ?>
</div>

<script>
    $(document).on('click', '#substitute-teacher', function(){
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds)) {
            $('#index-error-notification').html("Choose any lessons to substitute teacher").fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ ids: lessonIds });
            $.ajax({
                url    : '<?= Url::to(['teacher-substitute/index']) ?>?' +params,
                type   : 'get',
                success: function(response)
                {
                    if (response.status) {
                        $('#teacher-substitute-modal').modal('show');
                        $('#teacher-substitute-modal .modal-dialog').css({'width': '1000px'});
                        $('#teacher-substitute-content').html(response.data);
                    } else {
                        $('#index-error-notification').html("Choose lessons with same teacher").fadeIn().delay(5000).fadeOut();
                    }
                }
            });
            return false;
        }
    });
</script>

