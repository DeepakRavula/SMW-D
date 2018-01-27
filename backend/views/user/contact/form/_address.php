<?php

use yii\helpers\Html;
use common\models\Location;
use common\models\City;
use common\models\Label;
use common\components\select2\Select2;
use common\models\Province;
use common\models\Country;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
?>
<div class="row user-create-form">
	<?php
        $url = Url::to(['user-contact/edit-address', 'id' => $model->id]);
    if ($model->isNewRecord) {
        $url = Url::to(['user-contact/create-address','id' => $userModel->id]);
    }
    $form = ActiveForm::begin([
            'id' => 'address-form',
            'action' =>$url,
    ]);
    ?>
	<div class="row">
		<?php
        $locationModel = Location::findOne(['id' => Location::findOne(['slug' => \Yii::$app->location])->id]);
        ?>
		<?=
        $form->field($model, "labelId")->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Label::find()
                    ->user($userModel->id)
                    ->all(), 'id', 'name'),
            'options' => [
                'id' => 'address-label',
                'createNew' => true,
            ],
            'pluginOptions' => [
                'placeholder' => 'select label',
                'tags' => true,
            ],
        ])->label('Label');
        ?>
    <div style="display: none" class="hidden-field-address-label">
        <?= $form->field($model, "labelId")->textInput(['id' => 'address-label'])->label('Label'); ?>
    </div>
		<?= $form->field($addressModel, "address")->textInput(['maxlength' => true])->label('Address') ?>
		<?=
        $form->field($addressModel, "cityId")->widget(Select2::classname(), [
        'data' => ArrayHelper::map(City::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
        'options' => [
            'id' => 'city-label',
            'createNew' => true,
        ],
        'pluginOptions' => [
            'placeholder' => 'select city',
            'tags' => true
        ],
    ])->label('city');
        ?>
    <div style="display: none" class="hidden-field-city-label">
        <?= $form->field($addressModel, "cityId")->textInput(['id' => 'city-label'])->label('City'); ?>
    </div>
		<?=
        $form->field($addressModel, "countryId")->dropDownList(
            ArrayHelper::map(Country::find()->all(), 'id', 'name'),
            ['class' => 'country form-control']
        )->label('Country')
        ?>
		<?=
        $form->field($addressModel, "provinceId")->dropDownList(
            ArrayHelper::map(Province::find()->all(), 'id', 'name'),
            ['class' => 'province form-control']
        )->label('Province')
        ?>
		<?= $form->field($addressModel, "postalCode")->textInput(['maxlength' => true])->label('Postal Code') ?>
    </div>
	<div class="row pull-right">
		<?php echo Html::a('Cancel', '#', ['class' => 'btn btn-default address-cancel-btn']); ?>
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
	</div>
             <div class="pull-left">       
 <?php
                if (!$model->isNewRecord) {
                    echo Html::a('Delete', [
                '#', 'id' => $model->id
                ], [
                'id' => $model->id,
                'class' => 'user-contact-delete btn btn-danger',
            ]);
                }

        ?>
         </div>
	<?php ActiveForm::end(); ?>
</div>

<script>
 $(document).ready(function () {
	$(document).on('click', '.address-add-item', function () {
		var cityId = '<?= $locationModel->city_id; ?>';
		var countryId = '<?= $locationModel->country_id; ?>';
		var ProvinceId = '<?= $locationModel->province_id; ?>';
		$('.address-city').find('.city').val(cityId);
		$('.address').find('.country').val(countryId);
		$('.address').find('.province').val(ProvinceId);
		});
	});
</script>