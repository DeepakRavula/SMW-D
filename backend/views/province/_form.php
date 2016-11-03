<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Country;

/* @var $this yii\web\View */
/* @var $model common\models\Province */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="province-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-md-4">
			<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4 ">
			<?php echo $form->field($model, 'tax_rate')->textInput() ?>
		</div>
		<div class="col-md-4 ">
			<?php
            echo $form->field($model, 'country_id')->dropDownList(ArrayHelper::map(
                            Country::find()->all(), 'id', 'name'
            ))
            ?>
		</div>
	</div>


    <div class="form-group">
	<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		<?php 
            if (!$model->isNewRecord) {
                echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
            }
        ?>
    </div>

<?php ActiveForm::end(); ?>

</div>
