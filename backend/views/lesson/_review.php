<?php
use yii\helpers\Html;
use common\models\Program;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\TeacherAvailability;
use yii\data\ActiveDataProvider;
use yii\widgets\ActiveForm;
$this->title = 'Review Lessons';
?>
<style>
.e1Div{
  top: -46px;
  right: 0px;
}
</style>
<?php $form = ActiveForm::begin();?>
<div class="pull-right  m-r-20">
  <div class="schedule-index">
    <div class="e1Div">
      <?= $form->field($searchModel, 'showAllReviewLessons')->checkbox(['data-pjax' => true]); ?>
    </div>
  </div>
</div>
<?php ActiveForm::end(); ?>
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
</div>
  <h3>Teacher Availabilities</h3>
  <?php
    $locationId = Yii::$app->session->get('location_id');
    $query = TeacherAvailability::find()
    ->joinWith('userLocation')
    ->where(['user_id' => $courseModel->teacherId, 'location_id' => $locationId]);
    $teacherAvailabilityDataProvider = new ActiveDataProvider([
    'query' => $query,
    ]);
  ?>
  <?php
    echo GridView::widget([
      'dataProvider' => $teacherAvailabilityDataProvider,
      'options' => ['class' => 'col-md-5'],
      'tableOptions' => ['class' => 'table table-bordered table-more-condensed'],
      'headerRowOptions' => ['class' => 'bg-light-gray'],
      'columns' => [
          [
            'label' => 'Day',
            'value' => function ($data) {
            if (!empty($data->day)) {
            $dayList = TeacherAvailability::getWeekdaysList();
            $day   = $dayList[$data->day];
            return !empty($day) ? $day : null;
          }
            return null;
          },
          ],
        [
          'label' => 'From Time',
          'value' => function ($data) {
          return !empty($data->from_time) ? Yii::$app->formatter->asTime($data->from_time) : null;
          },
        ],
        [
          'label' => 'To Time',
          'value' => function ($data) {
          return !empty($data->to_time) ? Yii::$app->formatter->asTime($data->to_time) : null;
          },
        ],
      ],
    ]);
  ?>
<div class="clearfix"></div>
<?php
  $hasConflict = false;
  foreach ($conflicts as $conflict) {
    if (!empty($conflict)) {
      $hasConflict = true;
    break;
    }
  }
?>
<?php
  $columns = [
    [
      'label' => 'Date/Time',
      'class' => 'kartik\grid\EditableColumn',
      'attribute' => 'date',
      'format' => 'datetime',
      'refreshGrid' => true,
      'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
      'contentOptions' => ['class' => 'kv-sticky-column'],
    'editableOptions' => function ($model, $key, $index) {
      return [
        'header' => 'Lesson Date',
        'size' => 'md',
        'inputType' => \kartik\editable\Editable::INPUT_WIDGET,
        'widgetClass' => '\bootui\datetimepicker\DateTimepicker',
        'options' => [
        'format' => 'YYYY-MM-DD hh:mm A',
        'stepping' => 15,
      ],
      'formOptions' => ['action' => Url::to(['lesson/update-field'])],
      'pluginEvents' => [
        'editableError' => 'review.onEditableError',
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
    'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
    'contentOptions' => ['class' => 'kv-sticky-column'],
    'editableOptions' => function ($model, $key, $index) {
      return [
        'header' => 'Lesson Duration',
        'size' => 'md',
        'inputType' => \kartik\editable\Editable::INPUT_WIDGET,
        'widgetClass' => 'bootui\datetimepicker\Timepicker',
        'options' => [
        'format' => 'HH:mm',
        'stepping' => 15,
        ],
        'formOptions' => ['action' => Url::to(['lesson/update-field'])],
        'pluginEvents' => [
        'editableSuccess' => 'review.onEditableGridSuccess',
        ],
      ];
    },
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
  ];
?>
<?= \kartik\grid\GridView::widget([
'dataProvider' => $lessonDataProvider,
'pjax' => true,
'pjaxSettings' => [
'neverTimeout' => true,
'options' => [
'id' => 'review-lesson-listing',
],
],
'columns' => $columns,
'emptyText' => 'No conflicts here! You are ready to confirm!',
]); ?>
<div class="form-group">
  <div class="p-10 text-center">
    <?php if(! empty($vacationId)) :?>
    <?= Html::a('Confirm', ['confirm', 'courseId' => $courseId, 'Vacation[id]' => $vacationId, 'Vacation[type]' => $vacationType], [
    'class' => 'btn btn-danger',
    'id' => 'confirm-button',
    'disabled' => $hasConflict,
    'data' => [
    'method' => 'post',
    ],
    ]) ?>
    <?php elseif( ! empty($rescheduleBeginDate)):?>
    <?= Html::a('Confirm', ['confirm', 'courseId' => $courseId, 'Course[rescheduleBeginDate]' => $rescheduleBeginDate], [
    'class' => 'btn btn-danger',
    'id' => 'confirm-button',
    'disabled' => $hasConflict,
    'data' => [
    'method' => 'post',
    ],
    ]) ?>
    <?php elseif( ! empty($endDate)):?>
    <?= Html::a('Confirm', ['confirm', 'courseId' => $courseId, 'Enrolment[endDate]' => $endDate, 'Enrolment[type]' => $enrolmentEditType], [
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
    <?= Html::a('Cancel', ['course/index'], ['class' => 'btn']);
    ?>
    <?php endif; ?>
  </div>
</div>
<script>
var review = {
onEditableError: function(event, val, form, data) {
$(form).find('.form-group').addClass('has-error');
$(form).find('.help-block').text(data.message);
},
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
$("#lessonsearch-showallreviewlessons").on("change", function() {
var showAllReviewLessons = $(this).is(":checked");
var vacationId = '<?= $vacationId; ?>';
var vacationType = '<?= $vacationType; ?>';
var params = $.param({ 'LessonSearch[showAllReviewLessons]': (showAllReviewLessons | 0),
'Vacation[id]' : vacationId, 'Vacation[type]' : vacationType
});
var url = "<?php echo Url::to(['lesson/review', 'courseId' => $courseModel->id]); ?>?" + params;
$.pjax.reload({url:url,container:"#review-lesson-listing",replace:false,  timeout: 4000});  //Reload GridView
});
});
</script>