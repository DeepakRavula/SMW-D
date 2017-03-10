<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\Student;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\imperavi\Widget;
use common\models\User;
use yii\data\ActiveDataProvider;
use common\models\Lesson;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="student-form">
    <?php $form = ActiveForm::begin([
		'action' => Url::to(['enrolment/send-mail', 'id' => $model->id])
	]); ?>
		<div class="row">
        <div class="col-lg-12">
			<?php 
			$lessonDataProvider = new ActiveDataProvider([
				'query' => Lesson::find()
					->andWhere([
						'courseId' => $model->course->id,
						'status' => Lesson::STATUS_SCHEDULED
					])
					->notDeleted()
					->orderBy(['lesson.date' => SORT_ASC]),
					'pagination' => [
						'pageSize' => 60,
					 ],
			]);
			$data = ArrayHelper::map(User::find()->all(), 'email', 'email');
			$model->toEmailAddress = !empty($model->student->customer->email) ? $model->student->customer->email : null; 	
			$subject = 'Schedule for ' . $model->student->fullName;
			$body = null;
			?>
        	<?php $body = 'Please find the lesson schedule for the program you enrolled on ' . Yii::$app->formatter->asDate($model->course->startDate) ; 
			?>
			<?php $content = $this->render('content', [
				'toName' => $model->student->customer->publicIdentity,
				'content' => $body,
				'model' => $model,
				'lessonDataProvider' => $lessonDataProvider
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
