<?php

use yii\helpers\Html;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\jui\DatePicker;
use wbraganca\selectivity\SelectivityWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="invoice-form">

    <?php $form = ActiveForm::begin([
		'method' => 'get',
        'id' => 'customer-search-form',
		'action' => Url::to('/invoice/create'),
		]); ?>

<div class="row">
<div class="col-md-4">
    <?php $programs = ArrayHelper::map(User::find()
        ->join('INNER JOIN','user_location','user_location.user_id = user.id')
        ->join('INNER JOIN','rbac_auth_assignment','rbac_auth_assignment.user_id = user.id')
        ->where(['user_location.location_id' => Yii::$app->session->get('location_id'),'rbac_auth_assignment.item_name' => 'customer'])			
        ->all(),'id','userProfile.fullName' );
    ?>
    <?=
        $form->field($model, 'customer_id')->widget(SelectivityWidget::classname(), [
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => false,
                'items' => $programs,
                'value' => (empty($customer->id)) ? null : (string)$customer->id,
                'placeholder' => 'Select Customer',
            ]
        ]);
    ?>
</div>
</div>
<div class="clearfix"></div>
	 <?php echo $form->field($model, 'type')->hiddenInput()->label(false); ?>
    <div class="col-md-3">
        <?php echo $form->field($searchModel, 'fromDate')->widget(DatePicker::classname(), [
            'options'=>[
                'class' => 'form-control'
            ]   
        ]) ?>
    </div>
    <div class="col-md-3">
        <?php echo $form->field($searchModel, 'toDate')->widget(DatePicker::classname(), [
            'options'=>[
                'class' => 'form-control'
            ]
        ]) ?>
    </div>
    <div class="col-md-3 form-group m-t-5">
        <br>
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php echo $this->render('_uninvoiced_lessons', [
		'model'=>$model,
		'form'=>$form,
		'unInvoicedLessonsDataProvider' => $unInvoicedLessonsDataProvider,
        'customer' => $customer
    ]) ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<script>
$(document).ready(function() {
    $('#customer-search-form').on('change','#invoice-customer_id',  function(){ $('#customer-search-form').submit(); });
});
</script>