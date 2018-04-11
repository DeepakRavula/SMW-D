<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Student;
use yii\helpers\Url;
use yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="row user-create-form">
	<?php
    $session = Yii::$app->session;
    $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
    ?>
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['/student/update', 'id' => $model->id]),
        'id' => 'student-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validationUrl' => Url::to(['student/validate', 'id' => $model->id]),
        ]); ?>

    <div class="row">
            <?php echo $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
             <?php
            $customerName = $model->isNewRecord ? $customer->userProfile->lastname : null;
            $model->birth_date = !empty($model->birth_date) ? Yii::$app->formatter->asDate($model->birth_date) : null;
        ?>
            <?php echo $form->field($model, 'last_name')->textInput(['maxlength' => true, 'value' => $customerName]) ?>
            <?php //echo $form->field($model, 'birth_date')->textInput()?>
        <?=
           $form->field($model, 'birth_date')->widget(DatePicker::className(), [
              // 'name' => 'date_of_birth',
               //'language' => 'en-GB',
              'dateFormat' => 'php:d-m-Y',
               'clientOptions' => [
                   'changeMonth' => true,
                   'yearRange' => '1500:3000',
                   'changeYear' => true,
               ],
           ])->textInput(['placeholder' => 'Select Date']);

           ?>
				<?php if (!$model->isNewRecord) : ?>
					<?php echo $form->field($model, 'status')->dropDownList(Student::statuses()) ?>
				<?php endif; ?>
        </div>
	<?php echo $form->field($customer, 'id')->hiddenInput()->label(false); ?>
    <div class="row">
    <div class="pull-right">
        <?= Html::a('Cancel', '#', ['class' => 'btn btn-default student-profile-cancel-button']);
        ?>
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>

