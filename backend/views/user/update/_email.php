<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use common\models\Label;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */

$js = '
jQuery(".dynamicform_email").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_email .panel-title-email").each(function(index) {
        jQuery(this).html("Email: " + (index + 1))
    });
});

jQuery(".dynamicform_email").on("afterDelete", function(e) {
    jQuery(".dynamicform_email .panel-title-email").each(function(index) {
        jQuery(this).html("Email: " + (index + 1))
    });
});
';

$this->registerJs($js);
?>
<?php
    $form = ActiveForm::begin([
		'id' => 'email-form',
		'action' => Url::to(['user/edit-email', 'id' => $model->getModel()->id])	
	]);
    ?>
<?php
    DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_email', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.email-container-items', // required: css class selector
        'widgetItem' => '.email-item', // required: css class
        'limit' => 10, // the maximum times, an element can be cloned (default 999)
        'min' => 0, // 0 or 1 (default 1)
        'insertButton' => '.email-add-item', // css class
        'deleteButton' => '.email-remove-item', // css class
        'model' => $emailModels[0],
        'formId' => 'email-form',
        'formFields' => [
            'email',
            'labelId'
        ],
    ]);
    ?>
	<div class="row-fluid">
		<div class="col-md-12">
			<a href="#" class="btn btn-primary btn-xs add-email email-add-item"><i class="fa fa-plus"></i> Add</a>
		</div>
		<div class="email-container-items email-fields">
<?php foreach ($emailModels as $index => $emailModel): ?>
				<div class="item-block email-item"><!-- widgetBody -->
					<h4>
						<span class="panel-title-email m-r-10 pull-left">Email : <?= ($index + 1) ?></span>
						<em class="pull-left primary-contact"><?= $form->field($emailModel, "[{$index}]isPrimary")->checkbox() ?></em>
						<button type="button" class="pull-right email-remove-item btn btn-danger btn-xs"><i class="fa fa-remove"></i></button>
						<div class="clearfix"></div>
					</h4>
					<?php
                    // necessary for update action.
                    if (!$emailModel->isNewRecord) {
                        echo Html::activeHiddenInput($emailModel, "[{$index}]id");
                    }
                    ?>

	                <div class="row">
                        <div class="clearfix"></div>
	                    <div class="col-sm-4">
	<?= $form->field($emailModel, "[{$index}]email")->textInput(['maxlength' => true]) ?>
	                    </div>
	                    <div class="col-sm-4">
	<?= $form->field($emailModel, "[{$index}]labelId")->widget(Select2::classname(), [
                                    'data' => ArrayHelper::map(Label::find()
					->user($model->getModel()->id)
					->all(), 'id', 'name'),
                                    'options' => ['placeholder' => 'Select Label'],
                                    'pluginOptions' => [
                                        'tags' => true,
                                        'allowClear' => true,
                                    ],
                            ])->label('Label');
                            ?>
	                    </div>
	                    <div class="clearfix"></div>
	                </div>
				</div>
		<?php endforeach; ?>
				</div>
		</div>
    <div class="clearfix"></div>
		<hr class="hr-em right-side-faded">
		<?php DynamicFormWidget::end(); ?>
                <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
			<?php
                echo Html::a('Cancel', ['view', 'UserSearch[role_name]' => $model->roles, 'id' => $model->getModel()->id], ['class' => 'btn btn-default email-cancel-btn']);
        ?>
                <?php ActiveForm::end(); ?>
<script type="text/javascript">
$(document).ready(function(){
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
	$('.email-container-items').on('change', 'input[type="checkbox"]', function(){
		var checked = $(this).prop('checked');
		$('.email-container-items input[type="checkbox"]').prop('checked', false);

		if(checked) {
			$(this).prop('checked', true);
		} else {
			$(this).prop('checked', false);
		}
	});
});
</script>