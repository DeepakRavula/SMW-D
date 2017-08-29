<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Tabs;

$this->title = $model->student->fullName.' - '.$model->course->program->name;
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'EnrolmentSearch[showAllEnrolments]' => false], ['class' => 'go-back']);
?>
<div id="enrolment-enddate-alert" style="display: none;" class="alert-info alert fade in"></div>
<?= $this->render('_view-enrolment', [
    'model' => $model,
]);?>
    <div class="nav-tabs-custom">
<?php

    $lessonContent = $this->render('_lesson', [
        'model' => $model,
        'lessonDataProvider' => $lessonDataProvider,
    ]);

    $noteContent = $this->render('_payment-cycle', [
        'model' => $model,
        'paymentCycleDataProvider' => $paymentCycleDataProvider,
    ]);

    $items = [
        [
            'label' => 'Lesson',
            'content' => $lessonContent,
            'options' => [
                'id' => 'lesson',
            ],
        ],
        [
            'label' => 'Payment Cycle',
            'content' => $noteContent,
            'options' => [
                'id' => 'payment-cycle',
            ],
        ]
    ];

    echo Tabs::widget([
		'items' => $items,
	]);
?>
</div>
