<?php

use common\models\User;
use yii\helpers\Html;
use common\models\City;
use common\models\Province;
use common\models\Country;
use common\models\Address;
use common\models\PhoneNumber;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use wbraganca\selectivity\SelectivityWidget;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */


$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-phone").each(function(index) {
        jQuery(this).html("Phone Number: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-phone").each(function(index) {
        jQuery(this).html("Phone Number: " + (index + 1))
    });
});
';

$this->registerJs($js);
?>
<style>
    .box-body{
        padding-left: 0;
        padding-right: 0;
    }
    .address-fields, .phone-fields, .quali-fields{
        display: none;
    }
    hr{
        margin:10px 0;
    }
    .form-well{
        margin-bottom: 10px;
        padding-top: 15px;
    }
</style>

<div class="user-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
        <div class="col-md-6">
            <?php echo $form->field($model, 'firstname') ?>
        </div>
        <div class="col-md-6">
            <?php echo $form->field($model, 'lastname') ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-6">
            <?php echo $form->field($model, 'email') ?>
        </div>
        <div class="clearfix"></div>
        <hr>
        <!-- Address show hide -->
        <div class="row-fluid">
            <div class="col-md-12">
                <h4 class="pull-left m-r-20">Address</h4>
                <a href="#" class="add-address text-add-new"><i class="fa fa-plus-circle"></i> Add new address</a>
                <div class="clearfix"></div>
            </div>
            <div class="address-fields form-well">
                <!-- <h4>Your address line</h4> -->
                <div class="row">
                    <div class="col-md-4">
                        <?php echo $form->field($model, 'address',['inputOptions' => ['placeholder' => $model->getAttributeLabel('Address')]])->label(false) ?>
                    </div>
                    <div class="col-md-4">
                        <?php echo $form->field($model, 'addresslabel')->dropDownList(Address::labels(),['prompt'=>'Select Address Label'])->label(false) ?>
                    </div>
                    <div class="col-md-4">
                        <?php echo $form->field($model, 'city')->dropDownList(ArrayHelper::map(City::find()->all(),'id','name' ), ['prompt'=>'Select City'])->label(false) ?>
                    </div>
                    <div class="clearfix"></div>
            
                    <div class="col-md-4 no-label">
                        <?php echo $form->field($model, 'province')->dropDownList(ArrayHelper::map(Province::find()->all(),'id','name'), ['prompt'=>'Select Province'])->label('') ?>
                    </div>
                    <div class="col-md-4 no-label">
                        <?php echo $form->field($model, 'country')->dropDownList(ArrayHelper::map(Country::find()->all(),'id','name'), ['prompt'=>'Select Country'])->label('') ?>
                    </div>
                    <div class="col-md-4 no-label">
                        <?php echo $form->field($model, 'postalcode',['inputOptions' => ['placeholder' => $model->getAttributeLabel('Postal code')]])->label(false) ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr class="hr-ad">
        <!-- Phone show hide -->
        <div class="row-fluid">
            <div class="col-md-12">

    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => 4, // the maximum times, an element can be cloned (default 999)
        'min' => 0, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $phoneNumberModels[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'phonenumber',
            'phonelabel',
            'phoneextension',
        ],
    ]); ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-envelope"></i> Phone Numbers
            <button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus"></i> Add Phone</button>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body container-items"><!-- widgetContainer -->
            <?php foreach ($phoneNumberModels as $index => $phoneNumberModel): ?>
                <div class="item panel panel-default"><!-- widgetBody -->
                    <div class="panel-heading">
                        <span class="panel-title-phone">Phone Number: <?= ($index + 1) ?></span>
                        <button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <?php
                            // necessary for update action.
                            if (!$phoneNumberModel->isNewRecord) {
                                echo Html::activeHiddenInput($phoneNumberModel, "[{$index}]id");
                            }
                        ?>

                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($phoneNumberModel, "[{$index}]number")->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($phoneNumberModel, "[{$index}]label_id")->dropDownList(PhoneNumber::phoneLabels(), ['prompt'=>'Select Label']) ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($phoneNumberModel, "[{$index}]extension")->textInput(['maxlength' => true]) ?>
                            </div>
                        </div><!-- end:row -->
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php DynamicFormWidget::end(); ?>
			</div>
            <div class="phone-fields form-well">
                <!-- <h4>Add phone number</h4> -->
                <div class="row">
                    <div class="col-md-4">
                        <?php echo $form->field($model, 'phonenumber',['inputOptions' => ['placeholder' => $model->getAttributeLabel('Number')]])->label(false) ?>
                    </div>
                    <div class="col-md-4">
                        <?php echo $form->field($model, 'phonelabel')->dropDownList(PhoneNumber::phoneLabels(), ['prompt'=>'Select Label'])->label(false) ?>
                    </div>
                    <div class="col-md-4">
                        <?php echo $form->field($model, 'phoneextension',['inputOptions' => ['placeholder' => $model->getAttributeLabel('Extension')]
                    ])->label(false) ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr class="hr-ph">
        <!-- Qualification show hide -->
        <?php $userRoles = Yii::$app->authManager->getRolesByUser($model->model->id); $userRole = end($userRoles);?>
        <?php //if ( ! empty($userRole) && $userRole->name === User::ROLE_TEACHER): ?>
        <?php if ( ! empty($userRole) && $userRole->name === User::ROLE_TEACHER || $model->roles === User::ROLE_TEACHER): ?>
            <div class="row-fluid">
                <div class="col-md-12">
                    <h4 class="pull-left m-r-20">Qualifications</h4>
                    <a href="#" class="add-quali text-add-new"><i class="fa fa-plus-circle"></i> Add new qualification </a>
                    <div class="clearfix"></div>
                </div>
                <div class="quali-fields form-well">
                    <!-- <h4>Choose qualifications</h4> -->
                    <div class="row">
                        <div class="col-md-12">

							<?= $form->field($model, 'qualifications')->widget(SelectivityWidget::classname(),
							[
								'pluginOptions' => [
									'allowClear' => true,
									'multiple' => true,
									'items' =>$programs, 
									'value' => $model->qualifications,
									'placeholder' => 'No qualification selected'
								]
							]);?>
							
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <hr class="hr-qu">
		<?php endif;?>
        <div class="row-fluid">
        <div class="col-md-2">
            <?php echo $form->field($model, 'roles')->dropDownList($roles) ?>
        </div>
        <div class="col-md-2">
            <?php if( ! $model->getModel()->getIsNewRecord()) :?>
                <?php echo $form->field($model, 'status')->dropDownList(User::statuses(), ['options' => [2 => ['Selected'=>'selected']]]) ?>
            <?php endif;?>
        </div>
        </div>
        <div class="col-md-12">
            <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    $('.add-address').click(function(){
         $('.address-fields').show();
         $('.hr-ad').hide();
    });
    $('.add-phone').click(function(){
         $('.phone-fields').show();
         $('.hr-ph').hide();
    });
    $('.add-quali').click(function(){
         $('.quali-fields').show();
         $('.hr-qu').hide();
    });
</script>