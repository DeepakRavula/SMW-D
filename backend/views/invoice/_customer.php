<?php

use yii\helpers\Html;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use wbraganca\selectivity\SelectivityWidget;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\bootstrap\ActiveForm */
?>

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
			$form->field($model, 'customer_id')->widget(SelectivityWidget::classname(), [
				'pluginOptions' => [
					'allowClear' => true,
					'multiple' => false,
					'items' => $customers,
					'value' => (empty($customer->id)) ? null : (string) $customer->id,
					'placeholder' => 'Select Customer',
				],
			])->label(false);
		?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
		<?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary pull-left m-r-20']) ?>
		</div>
	</div>
<?php ActiveForm::end(); ?>
