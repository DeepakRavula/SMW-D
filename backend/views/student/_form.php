<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Student;
use yii\helpers\Url;
use common\models\Location;
use yii\jui\DatePicker;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="row user-create-form">
    <?php $locationId = Location::findOne(['slug' => \Yii::$app->location])->id; ?>
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['/student/update', 'id' => $model->id]),
        'id' => 'modal-form',
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
                'yearRange' => '-70:+0',
                'changeYear' => true,
            ],
            ]);
        ?>
        <?php $list = [0 => 'Not Specified', 1 => 'Male', 2 => 'Female']; ?>
        <?php $model->isNewRecord ? $model->gender = 0: $model->gender = $model->gender ;  ?>
        <?= $form->field($model, 'gender')->radioList($list); ?>
        <?= $form->field($model, 'note')->textarea(['rows' => '6']) ?>
        </div>
	<?php echo $form->field($customer, 'id')->hiddenInput()->label(false); ?>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).on('modal-success', function(event, params) {
        $.pjax.reload({container: "#student-profile", replace: false, timeout: 4000});
        return false;
    });
</script>

