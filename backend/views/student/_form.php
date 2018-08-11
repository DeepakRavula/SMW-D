<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Student;
use yii\helpers\Url;
use common\models\Location;
use yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="row user-create-form">
    <?php $locationId = Location::findOne(['slug' => \Yii::$app->location])->id; ?>
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['/student/update', 'id' => $model->id]),
        'id' => 'student-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validationUrl' => Url::to(['student/validate', 'id' => $model->id]),
        ]);
    ?>

    <div class="row">
        <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
        <?php $customerName = $model->isNewRecord ? $customer->userProfile->lastname : null;
        $model->birth_date = (new \DateTime($model->birth_date))->format('M d, Y')?>
        <?= $form->field($model, 'last_name')->textInput(['maxlength' => true, 'value' => $customerName]) ?>
        <?= $form->field($model, 'birth_date')->widget(DatePicker::className(), [
            'dateFormat' => 'php:M d, Y',
            'clientOptions' => [
                'defaultDate' => (new \DateTime($model->birth_date))->format('M d, Y'),
                'changeMonth' => true,
                'yearRange' => '-70:-4',
                'changeYear' => true,
            ],
            ])->textInput(['placeholder' => 'Select Date']);
        ?>
        </div>
	<?php echo $form->field($customer, 'id')->hiddenInput()->label(false); ?>
    <div class="row">
    <div class="pull-right">
        <?= Html::a('Cancel', '#', ['class' => 'btn btn-default student-profile-cancel-button']); ?>
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>
