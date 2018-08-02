<?php

use yii\helpers\Url;
use common\models\Lesson;
use backend\models\search\LessonSearch;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use common\components\gridView\KartikGridView;
use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Location;
use common\models\Student;
use common\models\UserProfile;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Private Lessons';
$this->params['action-button'] = $this->render('_action-menu', [
    'searchModel' => $searchModel
]);
$this->params['show-all'] = $this->render('_show-all-button', [
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
            ],
            [
                'label' => 'Program',
                'attribute' => 'program',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
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
                'label' => 'Teacher',
		        'attribute' => 'teacher',
                'value' => function ($data) {
                    return !empty($data->teacher->publicIdentity) ? $data->teacher->publicIdentity : null;
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
                            'format' => 'M d, Y',
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
                'filterWidgetOptions'=>[
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
                'label' => 'Price',
                'attribute' => 'price',
                'contentOptions' => ['class' => 'text-right'],
                'headerOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    return Yii::$app->formatter->asCurrency($data->netPrice);
                },
                ],
            [
                'label' => 'Owing',
                'attribute' => 'owing',
                'contentOptions' => function ($data) {
                   $highLightClass = 'text-right';
                   if ($data->isOwing($data->enrolment->id)) {
                    $highLightClass = 'text-right danger';
                   }
                    return ['class' => $highLightClass];
                },
                'headerOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    return Yii::$app->formatter->asCurrency($data->getOwingAmount($data->enrolment->id));
                },
            ]);

        if ((int) $searchModel->type === Lesson::TYPE_GROUP_LESSON) {
            array_shift($columns);
        }
     ?>   
    <div class="box">
    <?php echo KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['id' => 'lesson-index-1'],
        'filterModel' => $searchModel,
        'filterUrl' => Url::to(['lesson/index', 'LessonSearch[type]' => true, 'LessonSearch[showAll]' => $searchModel->showAll]),
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
    $(document).ready(function () {
        bulkAction.setAction();
    });

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

    $(document).off('click', '#lesson-discount').on('click', '#lesson-discount', function(){
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds)) {
            $('#index-error-notification').html("Choose any lessons to edit discount").fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ 'PrivateLesson[ids]': lessonIds });
            $.ajax({
                url    : '<?= Url::to(['private-lesson/apply-discount']) ?>?' +params,
                type   : 'get',
                success: function(response)
                {
                    if (response.status) {
                        $('#modal-content').html(response.data);
                        $('#popup-modal').modal('show');
                    }
		    else {
                                    if (response.message) {
                                        $('#index-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                                    }
                                }
                }
            });
            return false;
        }
    });

    $(document).off('change', '#lesson-index-1 .select-on-check-all, input[name="selection[]"]').on('change', '#lesson-index-1 .select-on-check-all, input[name="selection[]"]', function () {
        bulkAction.setAction();
        return false;
    });

    var bulkAction = {
        setAction: function() {
            var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
            if ($.isEmptyObject(lessonIds)) {
                $('#substitute-teacher').addClass('multiselect-disable');
                $('#lesson-discount').addClass('multiselect-disable');
                $('#lesson-delete').addClass('multiselect-disable');
                $('#lesson-duration-edit').addClass('multiselect-disable');
            } else {
                $('#substitute-teacher').removeClass('multiselect-disable');
                $('#lesson-discount').removeClass('multiselect-disable');
                $('#lesson-delete').removeClass('multiselect-disable');
                $('#lesson-duration-edit').removeClass('multiselect-disable');
            }
            return false;
        }
    };

    $(document).off('click', '#lesson-delete').on('click', '#lesson-delete', function(){
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds)) {
            $('#index-error-notification').html("Choose any lessons to delete").fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ 'PrivateLesson[ids]': lessonIds, 'PrivateLesson[isBulk]': true });
            bootbox.confirm({ 
                message: "Are you sure you want to delete this lesson?", 
                callback: function(result) {
                    if(result) {
                        $('.bootbox').modal('hide');
                        $.ajax({
                            url    : '<?= Url::to(['private-lesson/delete']) ?>?' +params,
                            type   : 'post',
                            success: function(response)
                            {
                                if (response.status) {
                                    if (response.message) {
                                        $('#index-success-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                                        $.pjax.reload({container: "#lesson-index", replace: false, async: false, timeout: 6000});
                                    }
                                } else {
                                    if (response.message) {
                                        $('#index-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                                    }
                                }
                            }
                        });
                    }
                }
            });	
        }
        return false;
    });
    $(document).off('click', '#lesson-duration-edit').on('click', '#lesson-duration-edit', function(){
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds)) {
            $('#index-error-notification').html("Choose any lessons to edit duration").fadeIn().delay(5000).fadeOut();
        } else {
            var params = $.param({ 'PrivateLesson[ids]': lessonIds, 'PrivateLesson[isBulk]': true });
                        $.ajax({
                            url    : '<?= Url::to(['private-lesson/edit-duration']) ?>?' +params,
                            type   : 'post',
                            success: function(response)
                            {    
                                if (response.status) {
                                        $('#modal-content').html(response.data);
                                        $('#popup-modal').modal('show');
                                    }

                                else {
                                    if (response.message) {
                                        $('#index-error-notification').text(response.message).fadeIn().delay(5000).fadeOut();
                                    }
                                }
                            }
                        });
                   
        }
        return false;
    });

    $(document).on('modal-success', function(event, params) {
        if (!$.isEmptyObject(params.url)) {
            window.location.href = params.url;
        } else if(params.status) {
            $.pjax.reload({container: "#lesson-index-1",timeout: 6000, async:false});
        }
        return false;
    });

    $(document).off('change', '#lessonsearch-showall').on('change', '#lessonsearch-showall', function(){
        var showAll = $(this).is(":checked");
        var params = $.param({'LessonSearch[type]': <?= Lesson::TYPE_PRIVATE_LESSON ?>,'LessonSearch[showAll]': (showAll | 0), 'LessonSearch[status]': '' });
        var url = "<?= Url::to(['lesson/index']); ?>?"+params;
        $.pjax.reload({url: url, container: "#lesson-index", replace: false, timeout: 4000});  //Reload GridView
    });  
</script>

