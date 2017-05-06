<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\Student;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\imperavi\Widget;
use common\models\User;
use common\models\Lesson;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="student-form">
    <?php $form = ActiveForm::begin([
		'action' => Url::to(['lesson/send-mail', 'id' => $model->id])
	]); ?>
		<div class="row">
        <div class="col-lg-12">
			<?php 
			$data = ArrayHelper::map(User::find()->all(), 'email', 'email');
			$students = Student::find()
				->joinWith('enrolment')
				->andWhere(['courseId' => $model->courseId])
				->all();
			$emails = ArrayHelper::getColumn($students, 'customer.email', 'customer.email');
			$model->toEmailAddress = $emails; 	
			$subject = $model->course->program->name . ' lesson reschedule';
			$body = null;
			$to = !empty($model->enrolment->student->customer->publicIdentity) ? $model->enrolment->student->customer->publicIdentity : null;
			?>
			<?php if($model->isRescheduled()) : ?> 
        	<?php $lesson = Lesson::findOne(['lesson.id' => $model->reschedule->lessonId]); ?>
			<?php  if(!empty($model->date) && new \DateTime($model->date) !== new \DateTime($lesson->date)) : ?>
			<?php 
			$duration		 = \DateTime::createFromFormat('H:i:s', $model->duration);
			$lessonDuration	 = ($duration->format('H') * 60) + $duration->format('i');
			$duration		 = \DateTime::createFromFormat('H:i:s', $lesson->duration);
			$oldLessonDuration	 = ($duration->format('H') * 60) + $duration->format('i');
			$body = $lesson->enrolment->student->fullname . '\'s ' . $lesson->course->program->name . ' lesson with ' . $lesson->teacher->publicIdentity . ' on ' . (new \DateTime($lesson->date))->format('l, F jS, Y') . ' @ ' . Yii::$app->formatter->asTime($lesson->date) . ' for ' . $oldLessonDuration . ' minutes has been rescheduled to ' . (new \DateTime($model->date))->format('l, F jS, Y') . ' @ ' . Yii::$app->formatter->asTime($model->date) . ' for ' . $lessonDuration . ' minutes.'; 
			?>
			<?php endif; ?>
			<?php endif; ?>
			<?php $content = $this->render('content', [
				'toName' => $to,
				'content' => $body,
				'model' => $model,
			]); 
			$model->content = $content; 
			?>
			 <?php echo $form->field($model, 'toEmailAddress')->widget(Select2::classname(), [
				 'data' => $data,
				'pluginOptions' => [
					'tags' => true,
					'allowClear' => true,
					'multiple' => true,
				],
        ]); ?>
        </div>
        </div>
		<div class="row">
        <div class="col-lg-12">
            <?php echo $form->field($model, 'subject')->textInput(['value' => $subject]) ?>
        </div>
        </div>
		<div class="row">
        <div class="col-lg-12">
            <?php echo $form->field($model, 'content')->widget(Widget::className(),
                [
					'plugins' => ['table'],
                    'options' => [
                        'minHeight' => 400,
                        'maxHeight' => 400,
                        'buttonSource' => true,
                        'convertDivs' => false,
                        'removeEmptyTags' => false,
                    ]
                ]
            ); ?>

        </div>
        </div>
    <div class="row-fluid">
    <div class="form-group col-lg-6">
       <?php echo Html::submitButton(Yii::t('backend', 'Send'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>
    <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
