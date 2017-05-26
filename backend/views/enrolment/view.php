<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Tabs;

$this->title = $model->student->fullName.' - '.$model->course->program->name;
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['student/view', 'id' => $model->student->id], ['class' => 'go-back text-add-new f-s-14 m-t-0 m-r-10']);
?>
<div class="tabbable-panel">
    <div class="tabbable-line">
<?php

    $detailContent = $this->render('_view-enrolment', [
        'model' => $model,
    ]);

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
            'label' => 'Details',
            'content' => $detailContent,
            'options' => [
                'id' => 'details',
            ],
        ],
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
</div>