<?php
use yii\helpers\Html;
use common\models\Program;
use yii\helpers\Url;
use kartik\grid\GridView;

$this->title = 'Review Lessons';
?>
<div class="user-details-wrapper">
	<div class="row">
    <?php if ((int) $courseModel->program->type === Program::TYPE_PRIVATE_PROGRAM) :?>  
	<div class="col-md-12">
		<p class="users-name"><?= $courseModel->enrolment->student->fullName; ?></p>
	</div>
    <?php endif; ?>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Program Name">
		<i class="fa fa-music detail-icon"></i> <?=    $courseModel->program->name; ?>
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Teacher name">
		<i class="fa fa-graduation-cap"></i> <?= $courseModel->teacher->publicIdentity; ?>
	</div>	
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Commencement Date">
			<i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDate($courseModel->startDate)?>	
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Renewal Date">
			<i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDate($courseModel->endDate)?>	
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Time">
		<i class="fa fa-clock-o"></i> <?php 
        $fromTime = \DateTime::createFromFormat('H:i:s', $courseModel->fromTime);
        echo $fromTime->format('h:i A'); ?>	
	</div>
	</div>
	<div class="clearfix"></div>	
	<?php
    $columns = [
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'date',
            'format' => 'date',
            'refreshGrid' => true,
            'headerOptions' => ['class' => 'kv-sticky-column'],
            'contentOptions' => ['class' => 'kv-sticky-column'],
            'editableOptions' => function ($model, $key, $index) {
                return [
                       'header' => 'Lesson Date',
                       'size' => 'md',
                       'inputType' => \kartik\editable\Editable::INPUT_WIDGET,
                       'widgetClass' => '\yii\jui\DatePicker',
                       'formOptions' => ['action' => Url::to(['lesson/update-field', 'id' => $model->id])],
                       'pluginEvents' => [
                           'editableSuccess' => 'review.onEditableGridSuccess',
                       ],
                   ];
            },
           ],
           [
               'class' => 'kartik\grid\EditableColumn',
               'attribute' => 'time',
               'refreshGrid' => true,
               'value' => function ($model, $key, $index, $widget) {
                   $lessonTime = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date)->format('H:i:s');

                   return Yii::$app->formatter->asTime($lessonTime);
               },
               'headerOptions' => ['class' => 'kv-sticky-column'],
               'contentOptions' => ['class' => 'kv-sticky-column'],
               'editableOptions' => function ($model, $key, $index) {
                   return [
                       'header' => 'Lesson From Time',
                       'size' => 'md',
                       'inputType' => \kartik\editable\Editable::INPUT_WIDGET,
                       'widgetClass' => 'dosamigos\datetimepicker\DateTimePicker',
                       'options' => [
                           'clientOptions' => [
                               'startView' => 1,
                               'minView' => 0,
                               'maxView' => 1,
                               'pickDate' => false,
                               'autoclose' => true,
                               'format' => 'HH:ii P',
                               'showMeridian' => true,
                               'minuteStep' => 15,
                           ],
                       ],
                       'formOptions' => ['action' => Url::to(['lesson/update-field', 'id' => $model->id])],
                       'pluginEvents' => [
                           'editableSuccess' => 'review.onEditableGridSuccess',
                       ],
                   ];
               },
               ],
            [
               'class' => 'kartik\grid\EditableColumn',
               'attribute' => 'duration',
               'refreshGrid' => true,
               'value' => function ($model, $key, $index, $widget) {
                   return (new \DateTime($model->duration))->format('H:i');
               },
               'headerOptions' => ['class' => 'kv-sticky-column'],
               'contentOptions' => ['class' => 'kv-sticky-column'],
               'editableOptions' => function ($model, $key, $index) {
                   return [
                       'header' => 'Lesson Duration',
                       'size' => 'md',
                       'inputType' => \kartik\editable\Editable::INPUT_WIDGET,
                       'widgetClass' => 'dosamigos\datetimepicker\DateTimePicker',
                       'options' => [
                           'clientOptions' => [
                               'startView' => 1,
                               'minView' => 0,
                               'maxView' => 3,
                               'pickDate' => false,
                               'autoclose' => true,
                               'format' => 'HH:ii',
                               'showMeridian' => true,
                               'minuteStep' => 15,
                           ],
                       ],
                       'formOptions' => ['action' => Url::to(['lesson/update-field', 'id' => $model->id])],
                       'pluginEvents' => [
                           'editableSuccess' => 'review.onEditableGridSuccess',
                       ],
                   ];
               },
            ],
            [
                'label' => 'Conflict',
                'value' => function ($data) use ($conflicts) {
                    if (!empty($conflicts[$data->id])) {
                        return 'Conflict';
                    }
                },
            ],
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'width' => '50px',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($model, $key, $index, $column) use ($conflicts) {
                    return Yii::$app->controller->renderPartial('_conflict-lesson', ['model' => $model, 'conflicts' => $conflicts[$model->id]]);
                },
                'headerOptions' => ['class' => 'kartik-sheet-style'],
                'expandOneOnly' => true,
            ],
    ]; ?>
    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) use ($conflicts) {
            if (!empty($conflicts[$model->id])) {
                return ['class' => 'danger'];
            }
        },
        'pjax' => true,
        'columns' => $columns,
        'showPageSummary' => true,
    ]); ?>

	<div class="form-group">
	<div class="p-10 text-center">
        <?php 
        $hasConflict = false;
        foreach ($conflicts as $conflictLessons) {
            foreach ($conflictLessons as $conflictLesson) {
                if ((!empty($conflictLesson['lessonIds'])) || (!empty($conflictLesson['dates']))) {
                    $hasConflict = true;
                    break;
                }
            }
        }
        ?>
		<?php if(! empty($vacationId)) :?>
		<?= Html::a('Confirm', ['confirm', 'courseId' => $courseId, 'Vacation[id]' => $vacationId, 'Vacation[type]' => $vacationType], [
            'class' => 'btn btn-danger',
            'id' => 'confirm-button',
            'disabled' => $hasConflict,
            'data' => [
                'method' => 'post',
            ],
        ]) ?>
		<?php elseif (!(empty($lessonFromDate) && empty($lessonToDate))):?>
		    <?= Html::a('Confirm', ['confirm', 'courseId' => $courseId, 'Course[lessonFromDate]' => $lessonFromDate, 'Course[lessonToDate]' => $lessonToDate], [
                'class' => 'btn btn-danger',
                'id' => 'confirm-button',
                'disabled' => $hasConflict,
                'data' => [
                    'method' => 'post',
                ],
            ]) ?> 
		
		<?php else :?>
		<?= Html::a('Confirm', ['confirm', 'courseId' => $courseId], [
            'class' => 'btn btn-danger',
            'id' => 'confirm-button',
            'disabled' => $hasConflict,
            'data' => [
                'method' => 'post',
            ],
        ]) ?>
		<?php endif; ?>
	<?php if ((int) $courseModel->program->type === Program::TYPE_PRIVATE_PROGRAM) :?>
			<?= Html::a('Cancel', ['student/view', 'id' => $courseModel->enrolment->studentId], ['class' => 'btn']);
            ?>
		<?php else :?>
		<?= Html::a('Cancel', ['course/view', 'id' => $courseModel->id], ['class' => 'btn']);
    ?>
   <?php endif; ?>
    </div>
</div>
	</div>
<script>   
var review = {
	onEditableGridSuccess :function(event, val, form, data) {
		$.ajax({
		url    : "<?php echo Url::to(['lesson/fetch-conflict', 'courseId' => $courseId]); ?>",
		type   : "GET",
        dataType: "json",
		success: function(response)
		{          
            if(response.hasConflict){
               $("#confirm-button").attr("disabled",true);
               $('#confirm-button').bind('click',false);
            } else {
               $("#confirm-button").removeAttr('disabled');
               $('#confirm-button').unbind('click',false); 
            }
		}
		});
		return true;     
    }
}
$(document).ready(function(){   
    if($('#confirm-button').attr('disabled')) {         
        $('#confirm-button').bind('click',false);
    }  
});
</script>