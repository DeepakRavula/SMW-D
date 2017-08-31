<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use common\models\PhoneNumber;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */

$js = '
jQuery(".dynamicform_phone").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_phone .panel-title-phone").each(function(index) {
        jQuery(this).html("Phone: " + (index + 1))
    });
});

jQuery(".dynamicform_phone").on("afterDelete", function(e) {
    jQuery(".dynamicform_phone .panel-title-phone").each(function(index) {
        jQuery(this).html("Phone: " + (index + 1))
    });
});
';

$this->registerJs($js);
?>
<?php
    $form = ActiveForm::begin([
		'id' => 'phone-form',
		'action' => Url::to(['/user/default/edit-phone', 'id' => $model->getModel()->id])	
	]);
    ?>
<?php
    DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_phone', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.phone-container-items', // required: css class selector
        'widgetItem' => '.phone-item', // required: css class
        'limit' => 10, // the maximum times, an element can be cloned (default 999)
        'min' => 0, // 0 or 1 (default 1)
        'insertButton' => '.phone-add-item', // css class
        'deleteButton' => '.phone-remove-item', // css class
        'model' => $phoneNumberModels[0],
        'formId' => 'phone-form',
        'formFields' => [
            'phonenumber',
            'phonelabel',
            'phoneextension',
        ],
    ]);
    ?>
	<div class="row-fluid">
		<div class="col-md-12">
			<a href="#" class="btn btn-primary btn-xs add-phone phone-add-item"><i class="fa fa-plus"></i> Add</a>
		</div>
		<div class="phone-container-items phone-fields">
<?php foreach ($phoneNumberModels as $index => $phoneNumberModel): ?>
				<div class="item-block phone-item"><!-- widgetBody -->
					<h4>
						<span class="panel-title-phone m-r-10 pull-left">Phone Number: <?= ($index + 1) ?></span>
						<em class="pull-left primary-contact"><?= $form->field($phoneNumberModel, "[{$index}]is_primary")->checkbox() ?></em>
						<button type="button" class="pull-right phone-remove-item btn btn-danger btn-xs"><i class="fa fa-remove"></i></button>
						<div class="clearfix"></div>
					</h4>
					<?php
                    // necessary for update action.
                    if (!$phoneNumberModel->isNewRecord) {
                        echo Html::activeHiddenInput($phoneNumberModel, "[{$index}]id");
                    }
                    ?>

	                <div class="row">
                        <div class="clearfix"></div>
	                    <div class="col-sm-4">
	<?= $form->field($phoneNumberModel, "[{$index}]number")->textInput(['maxlength' => true]) ?>
	                    </div>
	                    <div class="col-sm-4">
	<?= $form->field($phoneNumberModel, "[{$index}]label_id")->dropDownList(PhoneNumber::phoneLabels(), ['prompt' => 'Select Label']) ?>
	                    </div>
	                    <div class="col-sm-4">
	<?= $form->field($phoneNumberModel, "[{$index}]extension")->textInput(['maxlength' => true]) ?>
	                    </div>
	                </div>
				</div>
		<?php endforeach; ?>
				</div>
		</div>
		<?php DynamicFormWidget::end(); ?>
	<div class="row">
		<div class="col-md-12">
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
			<?php
                echo Html::a('Cancel', ['view', 'UserSearch[role_name]' => $model->roles, 'id' => $model->getModel()->id], ['class' => 'btn btn-default phone-cancel-btn']);
        ?>
		</div>
	</div>
	<?php ActiveForm::end(); ?>
<script type="text/javascript">
$(document).ready(function(){
	$('.phone-container-items').on('change', 'input[type="checkbox"]', function(){
		var checked = $(this).prop('checked');
		$('.phone-container-items input[type="checkbox"]').prop('checked', false);

		if(checked) {
			$(this).prop('checked', true);
		} else {
			$(this).prop('checked', false);
		}
	});
});
</script>