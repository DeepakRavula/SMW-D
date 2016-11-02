<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use common\models\Invoice;
use backend\models\search\InvoiceSearch;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
    <div class="col-md-2">
        <?php echo $form->field($model, 'fromDate')->widget(DatePicker::classname(), [
            'options' => [
                'class' => 'form-control',
            ],
]) ?>
    </div>
    <div class="col-md-2">
        <?php echo $form->field($model, 'toDate')->widget(DatePicker::classname(), [
            'options' => [
                'class' => 'form-control',
            ],
]) ?>
    </div>
	<?php if((int) $model->type === Invoice::TYPE_PRO_FORMA_INVOICE) : ?>
   <div class="col-md-2">
        <?php echo $form->field($model, 'mailStatus')->dropDownList(InvoiceSearch::mailStatuses())->label('Mail Status');?>
    </div>
		<div class="col-md-2">
        <?php echo $form->field($model, 'invoiceStatus')->dropDownList(InvoiceSearch::invoiceStatuses())->label('Invoice Status'); ?>
    </div>
		<?php endif; ?>
    <div class="col-md-3 form-group m-t-10">
        <?php echo $form->field($model, 'type')->hiddenInput()->label(false); ?>
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <div class="clearfix"></div>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
