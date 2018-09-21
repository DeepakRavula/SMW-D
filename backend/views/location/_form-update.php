<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\City;
use common\models\Province;
use common\models\Country;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use Carbon\Carbon;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use kartik\switchinput\SwitchInput;
/* @var $this yii\web\View */
/* @var $model common\models\Location */
/* @var $form yii\bootstrap\ActiveForm */
$this->title = 'Edit Location';
?>

<div class="location-form">
	<?php 
        $form = ActiveForm::begin([
        'id' => 'location-edit-form',
        'action' => Url::to(['/location-update']),
    ]); ?>
		<div class="row p-10">
		<div class="col-md-6">
			<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-6 ">
			<?php echo $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
		</div>
                <div class="clearfix"></div>
		<div class="col-md-6">
			<?= $form->field($model, 'phone_number')->widget(MaskedInput::className(), [
    'mask' => '(999) 999-9999',
]) ?>
		</div>
                <div class="col-md-6">
			<?php echo $form->field($model, 'email')->textInput() ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="row p-10">
		<div class="col-md-4">
			<?php
            echo $form->field($model, 'city_id')->dropDownList(ArrayHelper::map(
                            City::find()->notDeleted()->orderBy(['name' => SORT_ASC])->all(),
    'id',
    'name'
            ))
            ?>
		</div>
		<div class="col-md-4">
			<?php
            echo $form->field($model, 'province_id')->dropDownList(ArrayHelper::map(
                            Province::find()->orderBy(['name' => SORT_ASC])->all(),
                'id',
                'name'
            ))
            ?>
		</div>
		<div class="col-md-4">
			<?php
            echo $form->field($model, 'country_id')->dropDownList(ArrayHelper::map(
                            Country::find()->orderBy(['name' => SORT_ASC])->all(),
                'id',
                'name'
            ))
            ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="row p-10">
		<div class="col-md-4">
			<?php echo $form->field($model, 'postal_code')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4">
			<?php echo $form->field($model, 'royaltyValue')->textInput() ?>
		</div>
		<div class="col-md-4">
			<?php echo $form->field($model, 'advertisementValue')->textInput() ?>
		</div>
	</div>
	<div class="row p-10">
		<div class="col-md-4">
			<?php echo $form->field($model, 'conversionDate')->widget(DatePicker::classname(), [
                'options' => [
                    'value' => !empty($model->conversionDate) ? Carbon::parse($model->conversionDate)->format('d-m-Y') : ''
                ],
                'layout' => '{input}{picker}',
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy',
                ],
            ]);
            ?>
		</div>
			<div class = "col-md-6">
					<?= $form->field($model, 'isEnabledCron')->widget(SwitchInput::classname(), []);?> 
			</div>
		</div>
	<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12">
    <div class="pull-right">
		<?php echo Html::a('Cancel', '#', ['class' => 'btn btn-default location-cancel m-r-10' ]);?>
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
	</div>
        <div class="pull-left">
        <?php 
        if(Yii::$app->user->can('administrator')) {
        echo Html::a('Delete', ['delete', 'id' => $model->id], ['class' => 'btn btn-danger']); }?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

</div>
