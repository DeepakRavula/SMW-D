<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use common\models\Classroom;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentFrequencyDiscount */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="calendar-event-color-form">
    <div class="row p-20">
        <?php        
        $locationId = Yii::$app->session->get('location_id');
        if (empty ($teacherAvailabilityModel->id)) {
            $id = false;
        } else {
            $id = $teacherAvailabilityModel->id;
        }
        ?>
        <?php $form       = ActiveForm::begin([
            'id' => 'teacher-availability-form',
            'action' => Url::to(['user/modify-teacher-availability', 
                'resourceId' => $teacherAvailabilityModel->day, 'id' => $id,
                'teacherId' => $model->id]),
        ]); ?>
        
        <div class="col-md-6 form-group">
                <?= $form->field($teacherAvailabilityModel, 'from_time')->widget(TimePicker::classname(), [
                    'options' => [
                        'id' => 'teacher-availability-from-time'
                    ]]); ?>
        </div>
        <div class="col-md-6 form-group">
                <?= $form->field($teacherAvailabilityModel, 'to_time')->widget(TimePicker::classname(), ['options' => [
                        'id' => 'teacher-availability-to-time'
                    ]]); ?>
        </div>
        <div class="col-md-6 form-group">
            <?=
            $form->field($teacherAvailabilityModel, 'classroomId')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Classroom::find()->andWhere(['locationId' => $locationId])->all(),
                        'id', 'name'),
                'options' => ['placeholder' => 'Select Classroom', 'class' => 'form-control'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-12 p-l-20 form-group">
            <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'button']) ?>
        <?php if (!empty($teacherAvailabilityModel->id)) : ?>
            <?= Html::a('<i class="fa fa-close"></i> Delete', [
                    'delete-teacher-availability', 'id' => $teacherAvailabilityModel->id
                ],
                [
                    'class' => 'btn btn-primary',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ]
                ]); ?>
        <?php endif; ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>