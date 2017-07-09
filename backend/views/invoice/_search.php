<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use common\models\Invoice;
use backend\models\search\InvoiceSearch;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
	<?php if((int) $model->type === Invoice::TYPE_INVOICE) : ?>
    <div class="row">
    <div class="col-md-3">
        <?php echo $form->field($model, 'fromDate')->widget(DatePicker::classname(), [
            'options' => [
                'class' => 'form-control',
            ],
]) ?>
    </div>
    <div class="col-md-3">
        <?php echo $form->field($model, 'toDate')->widget(DatePicker::classname(), [
            'options' => [
                'class' => 'form-control',
            ],
]) ?>
    </div>
	<?php endif; ?>
	<?php if((int) $model->type === Invoice::TYPE_PRO_FORMA_INVOICE) : ?>
	<div class="col-md-3">
    <label>Due Date</label>
    <?php
        echo DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'dateRange',
            'convertFormat' => true,
            'initRangeExpr' => true,
            'pluginOptions' => [
                'autoApply' => true,
                'ranges' => [
                    Yii::t('kvdrp', 'Today') => ["moment().startOf('day')", "moment()"],
                    Yii::t('kvdrp', 'Tomorrow') => ["moment().startOf('day').add(1,'days')", "moment().endOf('day').add(1,'days')"],
					Yii::t('kvdrp', 'Next {n} Days', ['n' => 7]) => ["moment().startOf('day')", "moment().endOf('day').add(6, 'days')"],
                    Yii::t('kvdrp', 'Next {n} Days', ['n' => 30]) => ["moment().startOf('day')", "moment().endOf('day').add(29, 'days')"],
                ],
                'locale' => [
                    'format' => 'd-m-Y',
                ],
                'opens' => 'right',
            ],
        ]);
    ?>
    </div>
    <div class="col-md-3">
        <?php echo $form->field($model, 'mailStatus')->dropDownList(InvoiceSearch::mailStatuses(), ['prompt' => 'Select'])->label('Mail Status');?>
    </div>
    <div class="col-md-3">
        <?php echo $form->field($model, 'invoiceStatus')->dropDownList(InvoiceSearch::invoiceStatuses())->label('Invoice Status'); ?>
    </div>
        <?php endif; ?>
    <div class="col-md-2 form-group m-t-10">
        <?php echo $form->field($model, 'type')->hiddenInput()->label(false); ?>
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <div class="clearfix"></div>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
