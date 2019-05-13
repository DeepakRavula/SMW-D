<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
?>

<?php 
    $form = ActiveForm::begin([
        'id' => 'modal-form-lesson',
        'enableClientValidation' => false
    ]);
?>

<?php 
    $columns = [
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'label' => 'Original Date',
            'value' => function ($data) {
                $date = Yii::$app->formatter->asDate($data->getOriginalDate());
                $lessonTime = (new \DateTime($data->date))->format('H:i:s');
                return !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
            }
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'label' => 'Date',
            'value' => function ($data) {
                $date = Yii::$app->formatter->asDate($data->date);
                $lessonTime = (new \DateTime($data->date))->format('H:i:s');

                return !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
            }
        ],
        [
            'label' => 'Student',
            'value' => function ($data) {
                return !empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
            },
        ],
	    [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'attribute' => 'royaltyFree',
            'label' => 'Program',
            'value' => function ($model) {
                return $model->course->program->name;
            }
        ],
	    [
            'headerOptions' => ['class' => 'text-left'],
            'label' => 'Teacher',
            'value' => function ($data) {
                return $data->teacher->publicIdentity;
            }
        ],
	    [   
            'label' => 'Amount',
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency(round($data->privateLesson->total, 2));
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ],
        [   
            'label' => 'Payment',
            'value' => function ($data) use($model) {
                return Yii::$app->formatter->asCurrency(round($data->getPaidAmount($model->id), 2));
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ],
	    [
            'label' => 'Balance',
            'value' => function ($data) use ($model, $canEdit) {
                $balance = $data->privateLesson->balance;
                if ($canEdit) {
                    $balance += $data->getPaidAmount($model->id);
                }
                return Yii::$app->formatter->asCurrency(round($balance, 2));
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ],
    ];
    
    if ($canEdit) {
        array_push($columns, [
            'headerOptions' => ['class' => 'text-right', 'style' => 'width:180px'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:180px'],
            'label' => 'Payment',
            'value' => function ($data) use ($model, $form) {
                return $form->field($data, 'paymentAmount')->textInput([
                    'value' => round($data->getPaidAmount($model->id), 2),
                    'class' => 'form-control text-right payment-amount',
                    'id' => 'lesson-payment-' . $data->id,
                    'readOnly' => $data->hasCreditUsed($data->enrolment->id)
                ])->label(false);
            },
            'attribute' => 'new_activity',
            'format' => 'raw'
        ]);
    }
?>
<?php ActiveForm::end(); ?>

    <?php Pjax::Begin(['id' => 'lesson-listing', 'timeout' => 6000]); ?>
        <?= GridView::widget([
            'id' => 'lesson-grid',
            'dataProvider' => $lessonDataProvider,
            'columns' => $columns,
            'summary' => false,
            'emptyText' => false,
            'rowOptions' => ['class' => 'line-items-value lesson-line-items'],
            'options' => ['class' => 'col-md-12'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
        ]); ?>
    <?php Pjax::end(); ?>