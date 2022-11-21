<?php

use yii\helpers\Html;
use common\models\User;
use common\models\Invoice;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\jui\DatePicker;
use wbraganca\selectivity\SelectivityWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\bootstrap\ActiveForm */
$customer_id = (empty($customer->id)) ? null : (string) $customer->id;
?>

<div class="invoice-form">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'id' => 'customer-search-form',
        'action' => Url::to('/invoice/create'),
    ]); ?>

<div class="row">
<div class="col-md-3">
    <?php $customers = ArrayHelper::map(User::find()
        ->excludeWalkin()
        ->join('INNER JOIN', 'user_location', 'user_location.user_id = user.id')
        ->join('INNER JOIN', 'rbac_auth_assignment', 'rbac_auth_assignment.user_id = user.id')
        ->andWhere(['user_location.location_id' => Location::findOne(['slug' => \Yii::$app->location])->id, 'rbac_auth_assignment.item_name' => 'customer'])
        ->notDeleted()
        ->all(), 'id', 'userProfile.fullName');
    ?>
    <?=
        $form->field($model, 'customer_id')->widget(SelectivityWidget::classname(), [
            'pluginOptions' => [
                'multiple' => false,
                'items' => $customers,
                'value' => (empty($customer->id)) ? null : (string) $customer->id,
                'placeholder' => 'Select Customer',
            ],
        ]);
    ?>
</div>

<!-- <div class="clearfix"></div> -->
	 <?php echo $form->field($model, 'type')->hiddenInput()->label(false); ?>
<?php if ((int) $model->type === Invoice::TYPE_PRO_FORMA_INVOICE):?>
    <div class="col-md-3">        
        <?php echo $form->field($searchModel, 'fromDate')->widget(DatePicker::classname(), [
            'options' => [
                'class' => 'form-control',
            ],
        ]) ?>
    </div>
    <div class="col-md-3">
        <?php echo $form->field($searchModel, 'toDate')->widget(DatePicker::classname(), [
            'options' => [
                'class' => 'form-control',
            ],
        ]) ?>
    </div>
    <div class="col-md-1 form-group m-t-5">
        <br>
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>
<?php endif; ?>
</div>
    <?php ActiveForm::end(); ?>
    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'id' => 'customer-search-form',
        'action' => Url::to(['/invoice/create', 'Invoice[customer_id]' => $customer_id, 'Invoice[type]' => $model->type]),
    ]); ?>
    <?php echo $form->field($model, 'type')->hiddenInput()->label(false); ?>
    <?php echo $this->render('_uninvoiced_lessons', [
        'model' => $model,
        'form' => $form,
        'dataProvider' => $dataProvider,
        'searchModel' => $searchModel,
        'customer' => $customer,
    ]) ?>

    <div class="row">
    <div class="col-md-12">
        <div class="pull-right">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-info']) ?>
    </div>
    </div> 
    </div>
    <?php ActiveForm::end(); ?>

</div>
<script>
$(document).ready(function() {
    $('#customer-search-form').on('change','#invoice-customer_id',  function(){ $('#customer-search-form').submit(); });
});
</script>
