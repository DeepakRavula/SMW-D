<?php

use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\LocationAvailability;
use kartik\grid\GridView;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$hasConflict = false;
if ($conflictedLessonIdsCount > 0) {
    $hasConflict = true;
}
?>
<div id="modal-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div class="row">
    <div class="col-lg-6">
        <?= '<label class="control-label">Substitute Teacher</label>';
            echo Select2::widget([
                'name' => 'teacher',
                'data' => ArrayHelper::map($teachers, 
                    'id', 'userProfile.fullName'),
                'options' => [
                    'placeholder' => 'Select Substitute Teacher',
                    'id' => 'teacher-drop',
                    'class' => 'col-lg-6'
                ],
            ]);
        ?>
    </div>
    
<?php yii\widgets\Pjax::begin([
		'id' => 'review-lesson-listing',
		'timeout' => 6000,
	]) ?>
    
        <?php
$columns = [
    [
		'label' => 'Teacher',
		'value' => function ($model) {
			return $model->teacher->publicIdentity;
		},
		'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
		'contentOptions' => ['class' => 'kv-sticky-column'],
                ],
                [
		'label' => 'Program',
		'value' => function ($model) {
			return $model->course->program->name;
		},
		'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
		'contentOptions' => ['class' => 'kv-sticky-column'],
                ],
		[
		'label' => 'Date/Time',
		'attribute' => 'date',
		'format' => 'datetime',
		'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
		'contentOptions' => ['class' => 'kv-sticky-column', 'style' => 'width:150px;'],
                ],
		[
		'attribute' => 'duration',
		'value' => function ($model, $key, $index, $widget) {
			return (new \DateTime($model->duration))->format('H:i');
		},
		'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
		'contentOptions' => ['class' => 'kv-sticky-column', 'style' => 'width:80px;'],
	],
		[
                'format' => 'raw',
		'label' => 'Conflict',
		'headerOptions' => ['class' => 'bg-light-gray'],
		'value' => function ($data) use ($conflicts) {
			if (!empty($conflicts[$data->id])) {
				return current($conflicts[$data->id]);
			}
		},
	],
	[
		'class' => 'yii\grid\ActionColumn',
		'template' => '{edit}',
                'contentOptions' => ['style' => 'width:40px;'],
		'buttons' => [
                    'edit' => function  ($url, $model) {
                        return  Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>','#', [
                            'id' => 'edit-button', 'duration' => $model->duration,
                            'lessonId' => $model->id, 'teacherId' => $model->teacherId,
                            'programId' => $model->course->programId,
                            'class' => 'm-l-20'
                        ]);
                    },
		],
	],
];
?>
<div class="col-lg-12">
<?php if ($newLessonIds) : ?>
<?=
GridView::widget([
	'dataProvider' => $lessonDataProvider,
	'columns' => $columns,
	'emptyText' => 'No conflicts here! You are ready to confirm!',
]);
?>
<?php endif; ?>
</div>
<div class="col-lg-12">
<?php if ($newLessonIds) : ?> 
<div class="form-group pull-right">
    <?= Html::a('Cancel', null, ['class' => 'btn btn-default', 'id' => 'sub-teacher-cancel']); ?>
    <?=
    Html::a('Confirm', null, [
            'class' => 'btn btn-info',
            'id' => 'sub-teacher-confirm',
            'disabled' => $hasConflict
    ])
    ?>
</div>
</div>
<?php endif; ?> 
<?php \yii\widgets\Pjax::end(); ?>
</div>
<?php
Modal::begin([
	'header' => '<h4 class="m-0">Edit Lesson</h4>',
	'id'=>'sub-review-lesson-modal',
]); ?>
<div id="sub-review-lesson-content"></div>
<?php Modal::end();?>	

<?php
$locationId = \common\models\Location::findOne(['slug' => \Yii::$app->language])->id;
$minLocationAvailability = LocationAvailability::find()
    ->where(['locationId' => $locationId])
    ->orderBy(['fromTime' => SORT_ASC])
    ->one();
$maxLocationAvailability = LocationAvailability::find()
    ->where(['locationId' => $locationId])
    ->orderBy(['toTime' => SORT_DESC])
    ->one();
$minTime = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
$maxTime = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>
<script>
    $(document).off('click', '#edit-button').on('click', '#edit-button', function () {
        var event = $(this);
        var params = $.param({ id: event.attr('programid'), teacherId: event.attr('teacherid') });
        $.ajax({
            url    : '<?= Url::to(['program/teachers']) ?>?' + params,
            type   : 'get',
            success: function(response)
            {
                if(response.status)
                {
                    var teacherData = response.output;
                    if ($('#teacher-substitute-modal').modal('hide')) {
                        $('#spinner').show();
                        var lessonId = event.attr('lessonid');
                        var teacherId = response.selected;
                        var params = $.param({ id: teacherId });
                        $.ajax({
                            url: '<?= Url::to(['teacher-availability/availability-with-events']); ?>?' + params,
                            type: 'get',
                            dataType: "json",
                            success: function (response)
                            {
                                $('#spinner').hide();
                                var options = {
                                    selectConstraint: {
                                        start: '00:01', // a start time (10am in this example)
                                        end: '24:00', // an end time (6pm in this example)
                                        dow: [ 1, 2, 3, 4, 5, 6, 0 ]
                                    },
                                    eventConstraint: {
                                        start: '00:01', // a start time (10am in this example)
                                        end: '24:00', // an end time (6pm in this example)
                                        dow: [ 1, 2, 3, 4, 5, 6, 0 ]
                                    },
                                    teacherData: teacherData,
                                    lessonId: lessonId,
                                    date: moment(new Date()),
                                    teacherId: teacherId,
                                    duration: $('#edit-button').attr('duration'),
                                    businessHours: response.availableHours,
                                    minTime: '<?= $minTime; ?>',
                                    maxTime: '<?= $maxTime; ?>',
                                    eventUrl: '<?= Url::to(['teacher-availability/show-lesson-event']); ?>?teacherId=' + teacherId
                                };
                                $('#calendar-date-time-picker').calendarPicker(options);
                            }
                        });
                    }
                }
            }
        });
    });
    
    $(document).off('change', '#teacher-drop').on('change', '#teacher-drop', function () {
        var selectedValue = $(this).val();
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        var params = $.param({ ids: lessonIds, teacherId: selectedValue });
        var url = '<?= Url::to(['teacher-substitute/index']) ?>?' +params;
        $.ajax({
            url    : url,
            type   : 'get',
            success: function(response)
            {
                if(response.status)
                {
                    $.pjax.reload({url: url, container: '#review-lesson-listing', timeout: 6000});
                    if (response.hasConflicts) {
                        $("#sub-teacher-confirm").attr("disabled", true);
                        $(document).bind('click', '#sub-teacher-confirm', false);
                    } else {
                        $("#sub-teacher-confirm").removeAttr('disabled');
                        $(document).unbind('click', '#sub-teacher-confirm', false);
                    }
                }
            }
        });
        return false;
    });
    
    $(document).on('after-date-set', function(event, params) {
        $('#teacher-substitute-modal').modal('show');
        if (!$.isEmptyObject(params.date)) {
            var selectedValue = $('#teacher-drop').val();
            var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
            var param1 = $.param({ ids: lessonIds, teacherId: selectedValue, resolvingConflicts: true });
            var param2 = $.param({ id: params.lessonId });
            var url = '<?= Url::to(['teacher-substitute/index']) ?>?' +param1;
            $.ajax({
                url    : '<?= Url::to(['lesson/substitute']) ?>?' +param2,
                type   : 'post',
                dataType: "json",
                data   : $('#lesson-form').serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $.pjax.reload({url: url, container: '#review-lesson-listing', timeout: 6000});
                    }
                }
            });
            return false;
        }
    });
    
    $(document).on('after-picker-close', function() {
        $('#teacher-substitute-modal').modal('show');
    });
    
    $(document).off('click', '#sub-teacher-cancel').on('click', '#sub-teacher-cancel', function () {
        $('#teacher-substitute-modal').modal('hide');
        return false;
    });
    
    $(document).off('click', '#sub-teacher-confirm').on('click', '#sub-teacher-confirm', function () {
        if (!$(this).attr('disabled')) {
            var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
            var params = $.param({ ids: lessonIds });
            $.ajax({
                url    : '<?= Url::to(['teacher-substitute/confirm']) ?>?' + params,
                type   : 'get',
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#teacher-substitute-modal').modal('hide');
                        $.pjax.reload({url: response.url, container: '#lesson-index', timeout: 6000});
                        $('#index-success-notification').html("Lessons are substituted to the selected teachers").
                                    fadeIn().delay(5000).fadeOut();
                    }
                }
            });
            return false;
        } else {
            $('#modal-error-notification').html("Resolve all conflicts before confirm").fadeIn().delay(5000).fadeOut();
        }
    });
</script>