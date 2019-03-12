<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use common\models\Enrolment;
use yii\bootstrap\ActiveForm;
?>

<?php 
    $form = ActiveForm::begin([
        'id' => 'modal-form-group-lesson',
        'enableClientValidation' => false
    ]);
?>

<?php 
    $columns = [
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
            'attribute' => 'student',
            'value' => function ($data) use ($model) {
                $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->andWhere(['courseId' => $data->courseId])
                    ->customer($model->user_id)
                    ->one();
                return !empty($enrolment->student->fullName) ? $enrolment->student->fullName : null;
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
            'label' => 'Invoiced ?',
            'value' => function ($data) use ($model) {
                $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->andWhere(['courseId' => $data->courseId])
                    ->customer($model->user_id)
                    ->one();
                return $enrolment->hasInvoice($data->id) ? 'Yes' : 'No';
            }
        ],
	    [
            'label' => 'Amount',
            'value' => function ($data) use ($model) {
                $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->andWhere(['courseId' => $data->courseId])
                    ->customer($model->user_id)
                    ->one();
                return Yii::$app->formatter->asCurrency(round($data->getGroupNetPrice($enrolment), 2));
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ],
	    [
            'label' => 'Balance',
            'value' => function ($data) use ($model, $canEdit) {
                $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->andWhere(['courseId' => $data->courseId])
                    ->customer($model->user_id)
                    ->one();
                $balance = $data->getOwingAmount($enrolment->id);
                if ($canEdit) {
                    $balance += $data->getPaidAmount($model->id, $enrolment->id);
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
            'value' => function ($data) use ($form, $model) {
                $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->andWhere(['courseId' => $data->courseId])
                    ->customer($model->user_id)
                    ->one();
                return $form->field($data, 'paymentAmount')->textInput([
                    'value' => round($data->getPaidAmount($model->id, $enrolment->id), 2), 
                    'class' => 'form-control text-right payment-amount',
                    'id' => 'group-lesson-payment-' . $data->id,
                    'readOnly' => $data->hasCreditUsed($enrolment->id)
                ])->label(false);
            },
            'attribute' => 'new_activity',
            'format' => 'raw'
        ]);
    }
?>
<?php ActiveForm::end(); ?>

    <?php Pjax::Begin(['id' => 'group-lesson-listing', 'timeout' => 6000]); ?>
        <?= GridView::widget([
            'id' => 'group-lesson-grid',
            'dataProvider' => $lessonDataProvider,
            'columns' => $columns,
            'summary' => false,
            'emptyText' => false,
            'rowOptions' => ['class' => 'line-items-value group-lesson-line-items'],
            'options' => ['class' => 'col-md-12'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
        ]); ?>
    <?php Pjax::end(); ?>