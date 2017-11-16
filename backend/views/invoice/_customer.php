<?php

use yii\helpers\Html;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\select2\Select2Asset;
use yii\bootstrap\ButtonGroup;
use yii\helpers\Url;

Select2Asset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="m-b-10">
<?= ButtonGroup::widget([
	'buttons' => [
		Html::a('Customer', '', ['class' => ['btn btn-default active', 'customer'],
			'value' => 1]),
		Html::a('Walk-In', '', ['class' => ['btn btn-default', 'guest'],
			'value' => 2]),
	]
]); ?>
</div>
<br/>
<div id="customer">
    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'id' => 'customer-form',
		'action' => Url::to(['invoice/update-customer', 'id' => $model->id])
    ]); ?>

    <?php 
	$locationId = Yii::$app->session->get('location_id'); 
	$customers = ArrayHelper::map(User::find()
        ->join('INNER JOIN', 'user_location', 'user_location.user_id = user.id')
         ->join('LEFT JOIN', 'user_profile','user_profile.user_id = user_location.user_id')
        ->join('INNER JOIN', 'rbac_auth_assignment', 'rbac_auth_assignment.user_id = user.id')
        ->where(['user_location.location_id' => $locationId, 'rbac_auth_assignment.item_name' => 'customer'])
        ->notDeleted()
        ->orderBy('user_profile.firstname')
        ->all(), 'id', 'userProfile.fullName');
    ?>
	<div class="row">
		<div class="col-md-4">
		<?=
			 $form->field($model, "user_id")->widget(Select2::classname(), [
                                    'data' => $customers,
                                    'options' => ['placeholder' => 'Select customer'],
                                    'pluginOptions' => [
                                        'tags' => true,
                                               ],
                            ])->label('Label');
                            ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
             <div class="pull-right">
             <?= Html::a('Cancel', '#', ['class' => 'btn btn-default invoice-customer-update-cancel-button']);?> 
		<?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-info']) ?>
             </div>
             </div>
	</div>
<?php ActiveForm::end(); ?>
</div>
<div id="guest">
	<div class="guest-form p-10">

    <?php $form1 = ActiveForm::begin([
        'method' => 'post',
        'id' => 'walkin-customer-form',
		'action' => Url::to(['invoice/update-walkin', 'id' => $model->id])
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form1->field($userModel, 'firstname'); ?>
        </div>
        <div class="col-md-4">
            <?= $form1->field($userModel, 'lastname'); ?>
        </div>
        
        <div class="col-md-12">
            <div class="pull-right">
             <?= Html::a('Cancel', '#', ['class' => 'btn btn-default invoice-customer-update-cancel-button']);?>    
            <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-info m-r-20', 'name' => 'guest-invoice']) ?>
            </div>
            </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
</div>
