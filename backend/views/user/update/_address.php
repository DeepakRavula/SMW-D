<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use common\models\Location;
use common\models\Address;
use common\models\City;
use common\models\Province;
use common\models\Country;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */

$js = '
jQuery(".dynamicform_address").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_address .panel-title-address").each(function(index) {
        jQuery(this).html("Address: " + (index + 1))
    });
});

jQuery(".dynamicform_address").on("afterDelete", function(e) {
    jQuery(".dynamicform_address .panel-title-address").each(function(index) {
        jQuery(this).html("Address: " + (index + 1))
    });
});
';

$this->registerJs($js);
?>
<?php
$form = ActiveForm::begin([
	'id' => 'address-form',
	'action' => Url::to(['user/edit-address', 'id' => $model->getModel()->id])	
]);
?>
<?php
    DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_address', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.address-container-items', // required: css class selector
        'widgetItem' => '.address-item', // required: css class
        'limit' => 10, // the maximum times, an element can be cloned (default 999)
        'min' => 0, // 0 or 1 (default 1)
        'insertButton' => '.address-add-item', // css class
        'deleteButton' => '.address-remove-item', // css class
        'model' => $addressModels[0],
        'formId' => 'address-form',
        'formFields' => [
            'addresslabel',
            'address',
            'city',
            'country',
            'province',
            'postalcode',
        ],
    ]);
    ?>
    <div class="row-fluid">
		<div class="col-md-12">
			<a href="#" class="btn btn-primary btn-xs add-address address-add-item"><i class="fa fa-plus"> Add</i></a>
		</div>
		<div class="address-container-items address-fields">
<?php foreach ($addressModels as $index => $addressModel): ?>
				<div class="item-block address-item"><!-- widgetBody -->
					<h4>
						<span class="panel-title-address pull-left m-r-10">Address: <?= ($index + 1) ?></span>
						<em class="pull-left primary-contact"><?= $form->field($addressModel, "[{$index}]is_primary")->checkbox() ?></em>
						<button type="button" class="pull-right address-remove-item btn btn-danger btn-xs"><i class="fa fa-remove"></i></button>
						<div class="clearfix"></div>
					</h4>
					<?php
                    if (!$addressModel->isNewRecord) {
                        echo Html::activeHiddenInput($addressModel, "[{$index}]id");
                    }
                    $locationModel = Location::findOne(['id' => Yii::$app->session->get('location_id')]);
                    ?>

					<div class="row address-city">
						<div class="col-sm-4">
							<?= $form->field($addressModel, "[{$index}]label")->dropDownList(Address::labels(), ['prompt' => 'Select Label']) ?>
						</div>
						<div class="col-sm-4">
							<?= $form->field($addressModel, "[{$index}]address")->textInput(['maxlength' => true]) ?>
						</div>
						<div class="col-sm-4">
							<?=
                            $form->field($addressModel, "[{$index}]city_id")->dropDownList(
                                    ArrayHelper::map(City::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),['class' => 'city form-control'])
                            ?>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="row address">
						<div class="col-sm-4">
							<?=
                            $form->field($addressModel, "[{$index}]country_id")->dropDownList(
                                    ArrayHelper::map(Country::find()->all(), 'id', 'name'), ['class' => 'country form-control'])
                            ?>
						</div>
						<div class="col-sm-4">
							<?=
                            $form->field($addressModel, "[{$index}]province_id")->dropDownList(
                                    ArrayHelper::map(Province::find()->all(), 'id', 'name'), ['class' => 'province form-control'])
                            ?>
						</div>
						<div class="col-sm-4">
	<?= $form->field($addressModel, "[{$index}]postal_code")->textInput(['maxlength' => true]) ?>
						</div>

						<div class="clearfix"></div>
					</div><!-- end row -->
				</div><!-- widgetBody -->
	<?php endforeach; ?>
			</div><!-- widgetContainer -->
    </div>
<?php DynamicFormWidget::end(); ?>
	<div class="row">
		<div class="col-md-12">
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
			<?php
                echo Html::a('Cancel', ['view', 'UserSearch[role_name]' => $model->roles, 'id' => $model->getModel()->id], ['class' => 'btn btn-default address-cancel-btn']);
        ?>
		</div>
	</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
$(document).ready(function(){
	$(document).on('click', '.address-add-item', function(){
		var cityId = '<?= $locationModel->city_id; ?>';
		var countryId = '<?= $locationModel->country_id; ?>';
		var ProvinceId = '<?= $locationModel->province_id; ?>';
		$('.address-city').find('.city').val(cityId);
		$('.address').find('.country').val(countryId);
		$('.address').find('.province').val(ProvinceId);
	});
	$('.address-container-items').on('change', 'input[type="checkbox"]', function(){
		var checked = $(this).prop('checked');
		$('.address-container-items input[type="checkbox"]').prop('checked', false);

		if(checked) {
			$(this).prop('checked', true);
		} else {
			$(this).prop('checked', false);
		}
	});
});
</script>