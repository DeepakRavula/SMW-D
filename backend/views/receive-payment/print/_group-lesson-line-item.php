<?php

use common\components\gridView\KartikGridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\Location;
use yii\helpers\ArrayHelper;
use common\models\Student;
use common\models\Enrolment;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
?>

<?php 
    $form = ActiveForm::begin([
        'id' => 'modal-form-group-lesson',
        'enableClientValidation' => false
    ]);
?>

    <?php  
        $columns = [];
        if ($searchModel->showCheckBox) {
            array_push($columns, [
                'class' => 'yii\grid\CheckboxColumn',
                'contentOptions' => ['style' => 'width:30px;'],
                'checkboxOptions' => function($model, $key, $index, $column) {
                    return ['checked' => true,'class' =>'check-checkbox'];
                }
            ]);
        }

        array_push($columns, [
            'headerOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
            'attribute' => 'dateRange',
            'label' => 'Date',
            'value' => function ($data) {
                $date = Yii::$app->formatter->asDate($data->date);
                $lessonTime = (new \DateTime($data->date))->format('H:i:s');

                return !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
            }
        ]);

        array_push($columns, [
            'label' => 'Student',
            'attribute' => 'student',
            'value' => function ($data) use($searchModel) {
                $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->andWhere(['courseId' => $data->courseId])
                    ->customer($searchModel->userId)
                    ->one();
                return !empty($enrolment->student->fullName) ? $enrolment->student->fullName : null;
            },
        ]);

        array_push($columns, [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'attribute' => 'program',
            'label' => 'Program',
            'value' => function ($model) {
                return $model->course->program->name;
            }
        ]);

        array_push($columns, [
            'attribute' => 'teacher',
            'headerOptions' => ['class' => 'text-left'],
            'label' => 'Teacher',
            'value' => function ($data) {
                return $data->teacher->publicIdentity;
            }
        ]);

        array_push($columns, [
            'label' => 'Amount',
            'attribute' => 'amount',
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency($data->netPrice);
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ]);

        array_push($columns, [
            'attribute' => 'balance',
            'label' => 'Balance',
            'value' => function ($data) use($searchModel, $model) {
                $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->andWhere(['courseId' => $data->courseId])
                    ->customer($model->userId)
                    ->one();
                return Yii::$app->formatter->asCurrency($data->getOwingAmount($enrolment->id));
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right invoice-value']
        ]);

        if ($searchModel->showCheckBox && !$isCreatePfi) {
            array_push($columns, [
                'headerOptions' => ['class' => 'text-right', 'style' => 'width:180px'],
                'contentOptions' => ['class' => 'text-right', 'style' => 'width:180px'],
                'label' => 'Payment',
                'value' => function ($data) use($searchModel, $form, $model) {
                    $enrolment = Enrolment::find()
                        ->notDeleted()
                        ->isConfirmed()
                        ->andWhere(['courseId' => $data->courseId])
                        ->customer($model->userId)
                        ->one();
                    return $form->field($data, 'paymentAmount')->textInput([
                        'value' => round($data->getOwingAmount($enrolment->id), 2), 
                        'class' => 'form-control text-right payment-amount',
                        'id' => 'group-lesson-payment-' . $data->id
                    ])->label(false);
                },
                'attribute' => 'new_activity',
                'format' => 'raw',
            ]);
        }
    ?>
    <?php ActiveForm::end(); ?>

<?php Pjax::begin(['enablePushState' => false, 'id' => 'group-lesson-line-item-listing','timeout' => 6000,]); ?>
<?php if ($searchModel->showCheckBox) : ?>
    <?= GridView::widget([
        'options' => ['id' => 'group-lesson-line-item-grid'],
        'dataProvider' => $lessonLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => 'line-items-value group-lesson-line-items'],
        'emptyText' => 'No Lessons Available!'
    ]); ?>
<?php else: ?>
<?= GridView::widget([
        'options' => ['id' => 'group-lesson-line-item-grid'],
        'dataProvider' => $lessonLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => 'No Lessons Available!'
    ]); ?>
<?php endif; ?>
<?php Pjax::end(); ?>
