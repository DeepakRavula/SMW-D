<?php

use yii\helpers\Html;
use common\models\Label;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;

/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
?>
<div class="row user-create-form">
<?php
 $url = Url::to(['user-contact/edit-phone', 'id' => $model->id]);
    if ($model->isNewRecord) {
        $url = Url::to(['user-contact/create-phone','id' => $userModel->id]);
    }
$form = ActiveForm::begin([
        'id' => 'phone-form',
        'action' => $url,
    ]);
?>
<div class="row">
	<?= $form->field($phoneModel, 'number')->widget(MaskedInput::className(), [
    'mask' => '(999) 999-9999',
]) ?>
	<?=
    $form->field($model, "labelId")->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Label::find()
                ->user($userModel->id)
                ->all(), 'id', 'name'),
        'options' => [
            'id' => 'phone-label'
        ],
        'pluginOptions' => [
            'tags' => true
        ],
    ])->label('Label');
    ?>
	<?= $form->field($phoneModel, "extension")->textInput(['maxlength' => true]) ?>
    <?= $form->field($phoneModel, "note")->textarea(); ?>
</div>
    <div class="row">
        <div class="col-md-12">
<div class="pull-right">
	<?php echo Html::a('Cancel', '#', ['class' => 'm-r-10 btn btn-default phone-cancel-btn']); ?>
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
        </div></div>
<?php ActiveForm::end(); ?>
</div>
