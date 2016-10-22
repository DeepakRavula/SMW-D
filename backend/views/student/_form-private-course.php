<?php
use common\models\Course;
use drsdre\wizardwidget\WizardWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php
$programDetailContent = $this->render('_private-course-basic-detail', [
    'model' => new Course(),
]);

$calendarContent = $this->render('_calendar',[
    'model' => new Course(),
]);
?>
<?php
$wizard_config = [
    'id' => 'stepwizard',
    'steps' => [
        1 => [
            'title' => 'Step 1 - Choose Program',
            'icon' => 'glyphicon glyphicon-music',
            'content' => $programDetailContent,
            'buttons' => [
                'next' => [
                    'title' => 'Next',
                    'options' => [
                        'class' => 'disabled'
                    ],
                 ],
             ],
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

	