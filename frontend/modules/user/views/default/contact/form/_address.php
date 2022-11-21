<?php

use yii\helpers\Html;
use common\models\Location;
use common\models\City;
use common\models\Label;
use kartik\select2\Select2;
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
<div class="user-create-form">
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
            <div class="col-md-12">
		<?php
        $locationModel = Location::findOne(['id' => \common\models\Location::findOne(['slug' => \Yii::$app->location])->id]);
        ?>
		<?=
        $form->field($model, "labelId")->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Label::find()
                    ->user($userModel->id)
                    ->all(), 'id', 'name'),
            'options' => [
                'id' => 'address-label',
            ],
            'pluginOptions' => [
                'tags' => true,
            ],
        ])->label('Label');
        ?>
            </div>
            <div class="col-md-12">
		<?= $form->field($addressModel, "address")->textInput(['maxlength' => true])->label('Address') ?>
            </div>
             <div class="col-md-12">
		<?=
        $form->field($addressModel, "cityId")->dropDownList(
            ArrayHelper::map(City::find()->notDeleted()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
            ['class' => 'city form-control']
        )->label('City')
        ?>
            </div>
             <div class="col-md-12">
                        <?=
        $form->field($addressModel, "provinceId")->dropDownList(
            ArrayHelper::map(Province::find()->all(), 'id', 'name'),
            ['class' => 'province form-control']
        )->label('Province')
        ?>
                 </div>
             <div class="col-md-12">
		<?=
        $form->field($addressModel, "countryId")->dropDownList(
            ArrayHelper::map(Country::find()->all(), 'id', 'name'),
            ['class' => 'country form-control']
        )->label('Country')
        ?>
             </div>
		
		 <div class="col-md-12">
                        <?= $form->field($addressModel, "postalCode")->textInput(['maxlength' => true])->label('Postal Code') ?>
    </div>
        </div>
    <div class="row">
        <div class="col-md-12">
	<div class="pull-right">
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
            </div>
    </div>
	<?php ActiveForm::end(); ?>
</div>
<script>
 $(document).ready(function () {
	$(document).on('click', '.address-add-item', function () {
		var cityId = '<?= $userModel->userLocation->location->city_id; ?>';
		var countryId = '<?= $userModel->userLocation->location->country_id; ?>';
		var ProvinceId = '<?= $userModel->userLocation->location->province_id; ?>';
		$('.address-city').find('.city').val(cityId);
		$('.address').find('.country').val(countryId);
		$('.address').find('.province').val(ProvinceId);
		});
	});
</script>