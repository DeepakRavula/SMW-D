<?php

use common\models\Program;
use common\models\Enrolment;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use common\models\Location;
use common\models\TeacherAvailability;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="enrolment-form form-well form-well-smw">
	<?php $form = ActiveForm::begin(); ?>
    <div class="row">
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'duration')->widget(TimePicker::classname(), [
				'pluginOptions' => [
					'showMeridian' => false,
					'defaultTime' => date('H:i', strtotime('00:30')),
				]
			]);
			?>
		</div>
		<div class="col-md-4">
			<?php echo $form->field($model, 'programId')->dropDownList(
					ArrayHelper::map(Program::find()
						->active()
						->where(['type' => Program::TYPE_PRIVATE_PROGRAM])
						->all(), 
					'id', 'name'), ['prompt' => 'Select..']); ?>
		</div>
		<div class="col-md-4">
			<?php
			// Dependent Dropdown
			echo $form->field($model, 'teacherId')->widget(DepDrop::classname(), [
				'options' => ['id' => 'course-teacherid'],
				'pluginOptions' => [
					'depends' => ['course-programid'],
					'placeholder' => 'Select...',
					'url' => Url::to(['/course/teachers']),
					'onchange'=>'this.form.submit()',
				]
			]);
			?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
        	<?= $form->field($model, 'paymentFrequency')->radioList(Enrolment::paymentFrequencies())?>
		</div>
	</div>
    <div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>
<div class="clearfix"></div>
	<?php ActiveForm::end(); ?>

</div>
<?php
	$location = Location::findOne($id=Yii::$app->session->get('location_id'));
	$from_time = (new \DateTime($location->from_time))->format('H:i:s');
	$to_time = (new \DateTime($location->to_time))->format('H:i:s');
	$locationId			 = Yii::$app->session->get('location_id');
		$teachersWithClass	 = TeacherAvailability::find()
			->select(['user_location.user_id as id', "CONCAT(user_profile.firstname, ' ', user_profile.lastname) as name"])
			->distinct()
			->joinWith(['userLocation' => function($query) use($locationId) {
				$query->joinWith(['userProfile' => function($query) use($model){
					$query->joinWith(['lesson' => function($query) use($model){
						$query->where(['teacherId' => $model->teacherId])
							->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]]);
					}]);
				}])
				->where(['user_location.location_id' => $locationId]);
			}])
			->orderBy(['teacher_availability_day.id' => SORT_DESC])
			->one();

		$activeTeachers[] = [
			'id' => $teacherWithClass->id,
			'name' => $teacherWithClass->name,
		];

		$lessons =[];
        $lessons = Lesson::find()
			->joinWith(['course' => function($query) {
			    $query->andWhere(['locationId' => Yii::$app->session->get('location_id')]);
			}])
			->where(['teacherId' => $model->teacherId])
            ->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]])
            ->all();
       $events = [];
        foreach ($lessons as &$lesson) {
            $toTime = new \DateTime($lesson->date);
            $length = explode(':', $lesson->duration);
		    $toTime->add(new \DateInterval('PT' . $length[0] . 'H' . $length[1] . 'M'));
            if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                $title = $lesson->course->program->name . ' ( ' . $lesson->course->getEnrolmentsCount() . ' ) ';
            } else {
            	$title = $lesson->enrolment->student->fullName . ' ( ' .$lesson->course->program->name . ' ) ';
			}
            $events[]= [
                'resources' => $lesson->teacherId,
                'title' => $title,
                'start' => $lesson->date,
                'end' => $toTime->format('Y-m-d H:i:s'),
            ];
        }
        unset($lesson);
		
			
?>
<?php $form = ActiveForm::begin(); ?>
    <?php echo $this->render('_calendar', [
		'from_time' => $from_time,
		'to_time' =>  $to_time,
		'teachersWithClass' => $teachersWithClass,
		'events' => $events,
    ]) ?>
<?php ActiveForm::end(); ?>
