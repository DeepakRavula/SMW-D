<?php

use yii\helpers\Html;
use common\models\User;
use common\models\UserProfile;
use common\models\UserLocation;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="invoice-form">

    <?php $form = ActiveForm::begin([
		'method' => 'get',
		'action' => Url::to('/invoice/create'),
		]); ?>

    <?php echo $form->errorSummary($model); ?>

<?php echo $form->field($model, 'customer_id')->dropDownList(
		ArrayHelper::map(
				User::find()
					->join('INNER JOIN','user_location','user_location.user_id = user.id')
					->join('INNER JOIN','rbac_auth_assignment','rbac_auth_assignment.user_id = user.id')
					->where(['user_location.location_id' => Yii::$app->session->get('location_id'),'rbac_auth_assignment.item_name' => 'customer'])			
				->all(),
		'id','userProfile.fullName' ), ['prompt'=>'Select Customer', 'onchange'=>'this.form.submit()'] )->label(false) ?>

    <?php ActiveForm::end(); ?>


    <?php echo $this->render('_uninvoiced_lessons', [
		'unInvoicedLessonsDataProvider' => $unInvoicedLessonsDataProvider,
    ]) ?>
</div>
