<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Country;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Province */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="province-form">

	<?php   $url = Url::to(['province/update', 'id' => $model->id]);
            if ($model->isNewRecord) {
               $url = Url::to(['province/create']);
            }
        $form = ActiveForm::begin([
        'id' => 'province-form',
        'action' => $url,
    ]); ?>


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
                            Country::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'
            ))
            ?>
		</div>
	</div>


    <div class="form-group">
	<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		<?php 
            if (!$model->isNewRecord) {
                echo Html::a('Cancel', '#', ['class' => 'province-cancel btn btn-default']);
            }
        ?>
    </div>

<?php ActiveForm::end(); ?>

</div>
