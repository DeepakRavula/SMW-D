<?php

use common\models\Course;
use drsdre\wizardwidget\WizardWidget;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php
$form					 = ActiveForm::begin([
	'id' => 'enrolment-form',
	]);
?>

<?php
$programDetailContent	 = $this->render('_private-course-basic-detail',
	[
	'model' => new Course(),
	'form' => $form,
	]);

$calendarContent = $this->render('_calendar', [
	'model' => new Course(),
	'form' => $form,
	]);
?>
<?php
$wizard_config	 = [
	'id' => 'stepwizard',
	'steps' => [
		1 => [
			'class' => 'step-1',
			'title' => 'Step 1 - Choose Program',
			'icon' => 'glyphicon glyphicon-music',
			'content' => $programDetailContent,
		],
		2 => [
			'title' => 'Step 2 - Choose Teacher',
			'icon' => 'glyphicon glyphicon-education',
			'content' => $calendarContent,
		],
	],
];
?>
<?= WizardWidget::widget($wizard_config); ?>
<?php ActiveForm::end(); ?>
