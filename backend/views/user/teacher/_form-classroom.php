<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use wbraganca\selectivity\SelectivityWidget;
use common\models\Classroom;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentFrequencyDiscount */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="calendar-event-color-form">
    <div class="row p-20">
        <?php $form       = ActiveForm::begin(['id' => 'classroom-assign-form']); ?>
        <?php $locationId = Yii::$app->session->get('location_id'); ?>
        <div class="col-md-12 form-group">
            <?=
            $form->field($roomModel, 'teacherAvailabilityId')->hiddenInput([
                'id' => 'teacher-availability-id'])->label(false);
            ?>
            <?=
            $form->field($roomModel, 'classroomId')->widget(SelectivityWidget::classname(),
                [
                'pluginOptions' => [
                    'allowClear' => true,
                    'items' => ArrayHelper::map(Classroom::find()->andWhere(['locationId' => $locationId])->all(),
                        'id', 'name'),
                    'placeholder' => 'Select Classroom',
                ],
            ]);
            ?>
        </div>
        <div class="col-md-12 p-l-20 form-group">
            <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'button']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>