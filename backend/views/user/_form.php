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

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
        <?php echo $form->field($model, 'firstname') ?>
        <?php echo $form->field($model, 'lastname') ?>
        <?php echo $form->field($model, 'email') ?>
        <?php echo $form->field($model, 'password')->passwordInput() ?>
        <?php echo $form->field($model, 'address',['inputOptions' => ['placeholder' => $model->getAttributeLabel('Address')],]) ?>
        <?php echo $form->field($model, 'addresslabel')->dropDownList(Address::labels(),['prompt'=>'Select Address Label'])->label(false) ?>
		<?php echo $form->field($model, 'city')->dropDownList(ArrayHelper::map(City::find()->all(),'id','name' ), ['prompt'=>'Select City'])->label(false) ?>
		<?php echo $form->field($model, 'province')->dropDownList(ArrayHelper::map(Province::find()->all(),'id','name'), ['prompt'=>'Select Province'])->label(false) ?>
        <?php echo $form->field($model, 'postalcode',['inputOptions' => ['placeholder' => $model->getAttributeLabel('Postal code')]])->label(false) ?>
	    <?php echo $form->field($model, 'country')->dropDownList(ArrayHelper::map(Country::find()->all(),'id','name'), ['prompt'=>'Select Country'])->label(false) ?>
        <?php echo $form->field($model, 'phonenumber',['inputOptions' => ['placeholder' => $model->getAttributeLabel('Number')],]) ?>
        <?php echo $form->field($model, 'phonelabel')->dropDownList(PhoneNumber::phoneLabels(), ['prompt'=>'Select Label'])->label(false) ?>
        <?php echo $form->field($model, 'phoneextension',['inputOptions' => ['placeholder' => $model->getAttributeLabel('Extension')]
		])->label(false) ?>
        <?php echo $form->field($model, 'status')->dropDownList(User::statuses(), ['options' => [2 => ['Selected'=>'selected']]]) ?>
		
		<?php $userRoles = Yii::$app->authManager->getRolesByUser($model->model->id); $userRole = end($userRoles);?>
		<?php //if ( ! empty($userRole) && $userRole->name === User::ROLE_TEACHER): ?>
	<?php if ( ! empty($userRole) && $userRole->name === User::ROLE_TEACHER || $model->roles === User::ROLE_TEACHER): ?>
       		<?php echo $form->field($model, 'qualifications')->checkboxList($programs) ?>
		<?php endif;?>
        <?php echo $form->field($model, 'roles')->dropDownList($roles) ?>
        <div class="form-group">
            <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div>
