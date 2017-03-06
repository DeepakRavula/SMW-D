<?php
use yii\helpers\Html;
use common\models\Enrolment;
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
	<div class="col-md-12">
		<p class="users-name"><?= $enrolment->student->fullName; ?></p>
	</div>
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
	<div class="row teacher-availability">
		<?php
		$locationId = Yii::$app->session->get('location_id');
		$query = TeacherAvailability::find()
			->joinWith('userLocation')
			->where(['user_id' => $courseModel->teacherId, 'location_id' => $locationId]);
		$teacherAvailabilityDataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		?>
		<?php yii\widgets\Pjax::begin() ?>
		<h4>Availabilities </h4>
		<?php
		echo GridView::widget([
			'dataProvider' => $teacherAvailabilityDataProvider,
			'options' => ['class' => 'col-md-5'],
			'tableOptions' => ['class' => 'table table-bordered'],
			'headerRowOptions' => ['class' => 'bg-light-gray'],
			'columns' => [
				[
					'label' => 'Day',
					'value' => function ($data) {
						if (!empty($data->day)) {
							$dayList = TeacherAvailability::getWeekdaysList();
							$day	 = $dayList[$data->day];

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
		<?php \yii\widgets\Pjax::end(); ?>
	</div>
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
            'attribute' => 'date',
            'format' => 'date',
        ],
        [
            'attribute' => 'time',
            'value' => function ($model, $key, $index, $widget) {
                $lessonTime = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date)->format('H:i:s');
        	        return Yii::$app->formatter->asTime($lessonTime);
            },
        ],
        [
		   'attribute' => 'duration',
		   'value' => function ($model, $key, $index, $widget) {
			   return (new \DateTime($model->duration))->format('H:i');
		    },
        ],
		[
			'label' => 'Conflict',
			'value' => function ($data) use ($conflicts) {
				if (!empty($conflicts[$data->id])) {
					return current($conflicts[$data->id]);
				}
			},
		],
    ]; ?>
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
		<?= Html::a('Confirm', ['confirm-group-enrolment', 'enrolmentId' => $enrolment->id], [
            'class' => 'btn btn-danger',
            'id' => 'confirm-button',
            'disabled' => $hasConflict,
            'data' => [
                'method' => 'post',
            ],
        ]) ?>
			<?= Html::a('Cancel', ['student/view', 'id' => $enrolment->studentId], ['class' => 'btn']); ?>
    </div>
</div>
	</div>
<script>
$(document).ready(function(){
    if($('#confirm-button').attr('disabled')) {
        $('#confirm-button').bind('click',false);
    }
	$("#lessonsearch-showallreviewlessons").on("change", function() {
        var showAllReviewLessons = $(this).is(":checked");
        var url = "<?php echo Url::to(['lesson/group-enrolment-review', 'courseId' => $courseModel->id, 'enrolmentId' => $enrolment->id]); ?>?LessonSearch[showAllReviewLessons]=" + (showAllReviewLessons | 0);
        $.pjax.reload({url:url,container:"#review-lesson-listing",replace:false,  timeout: 4000});  //Reload GridView
    });
});
</script>
