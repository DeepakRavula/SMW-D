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
	<?php if((int) $model->type === Invoice::TYPE_PRO_FORMA_INVOICE) : ?>
    <div class="col-md-3">
        <?php echo $form->field($model, 'mailStatus')->dropDownList(InvoiceSearch::mailStatuses())->label('Mail Status');?>
    </div>
    <div class="col-md-3">
        <?php echo $form->field($model, 'invoiceStatus')->dropDownList(InvoiceSearch::invoiceStatuses())->label('Invoice Status'); ?>
    </div>
    <div class="col-md-3">
    <label>Due Date Range</label>
    <?php
        echo DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'dateRange',
            'convertFormat' => true,
            'initRangeExpr' => true,
            'pluginOptions' => [
                'autoApply' => true,
                'ranges' => [
                    Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["moment().startOf('day').subtract(6, 'days')", 'moment()'],
                    Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["moment().startOf('day').subtract(29, 'days')", 'moment()'],
                    Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')", "moment().endOf('month')"],
                    Yii::t('kvdrp', 'Last Month') => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                ],
                'locale' => [
                    'format' => 'd-m-Y',
                ],
                'opens' => 'right',
            ],
        ]);
    ?>
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
