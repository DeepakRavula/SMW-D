<?php

use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\LocationAvailability;
use common\components\gridView\AdminLteGridView;
use kartik\datetime\DateTimePickerAsset;
DateTimePickerAsset::register($this);
require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup.php';
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

<div class="row">
    <div class="col-md-5">
        <?= Html::dropDownList('teacher', null, ArrayHelper::map($teachers, 
                'id', 'userProfile.fullName'), [ 'prompt' => 'Select Substitute Teacher',
                    'id' => 'teacher-drop', 'class' => 'form-control'])
        ?>
    </div>
<?php yii\widgets\Pjax::begin([
		'id' => 'review-lesson-listing',
		'timeout' => 6000,
	]) ?>
    <div class="col-md-12">
        <?php
$columns = [
		[
		'label' => 'Date/Time',
		'attribute' => 'date',
		'format' => 'datetime',
		'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
		'contentOptions' => ['class' => 'kv-sticky-column'],
	],
		[
		'attribute' => 'duration',
		'value' => function ($model, $key, $index, $widget) {
			return (new \DateTime($model->duration))->format('H:i');
		},
		'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
		'contentOptions' => ['class' => 'kv-sticky-column'],
	],
		[
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
		'buttons' => [
			'edit' => function  ($url, $model) {
				return  Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>','#', [
					'id' => 'edit-button',
					'class' => 'm-l-20'
				]);
			},
		],
	],
];
?>
	
<?php if ($newLessonIds) : ?>
<?=
AdminLteGridView::widget([
	'dataProvider' => $lessonDataProvider,
	'columns' => $columns,
	'emptyText' => 'No conflicts here! You are ready to confirm!',
]);
?>
<?php endif; ?>
    </div>
    
</div>
<?php if ($newLessonIds) : ?> 
<div class="form-group">
    <?=
    Html::a('Confirm', null, [
            'class' => 'btn btn-info',
            'id' => 'sub-confirm-button',
            'disabled' => $hasConflict,
            'data' => [
                    'method' => 'post',
            ],
    ])
    ?>
    <?= Html::a('Cancel', null, ['class' => 'btn btn-default', 'id' => 'sub-teacher-cancel']); ?>
</div>
<?php endif; ?> 
<?php \yii\widgets\Pjax::end(); ?>
<?php
Modal::begin([
	'header' => '<h4 class="m-0">Edit Lesson</h4>',
	'id'=>'sub-review-lesson-modal',
]); ?>
<div id="sub-review-lesson-content"></div>
<?php Modal::end();?>	

<?php
$locationId = Yii::$app->session->get('location_id');
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
    $(document).on('click', '#edit-button', function () {
        if ($('#teacher-substitute-modal').modal('hide')) {
            var teacherId = $('#teacher-drop').val();
            var params = $.param({ id: teacherId });
            $.ajax({
                url: '<?= Url::to(['teacher-availability/availability-with-events']); ?>?' + params,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    $('#spinner').hide();
                    var options = {
                        date: moment(new Date()),
                        duration: '00:30:00',
                        businessHours: response.availableHours,
                        minTime: '<?= $minTime; ?>',
                        maxTime: '<?= $maxTime; ?>',
                        eventUrl: '<?= Url::to(['teacher-availability/show-lesson-event']); ?>?teacherId=' + teacherId
                    };
                    $('#calendar-date-time-picker').calendarPicker(options);
                }
            });
            return false;
        }
    });
    
    $(document).on('change', '#teacher-drop', function () {
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
                }
            }
        });
        return false;
    });
    
    $(document).ready(function () {
        if ($('#sub-confirm-button').attr('disabled')) {
            $('#sub-confirm-button').bind('click', false);
        }
    });
    
    $(document).on('click', '#sub-teacher-cancel', function () {
        $('#teacher-substitute-modal').modal('hide');
        return false;
    });
</script>