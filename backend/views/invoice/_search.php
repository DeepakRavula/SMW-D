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
    <div class="row">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
	<?php if ((int) $model->type === Invoice::TYPE_INVOICE) : ?>
	<?php $class = 'invoice';?>
    <div class="col-md-3">
        <?php
        echo DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'invoiceDateRange',
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
                    'format' => 'M d,Y',
                ],
                'opens' => 'right',
            ],
        ]);
        ?>
    </div>
	<?php endif; ?>
	<?php if ((int) $model->type === Invoice::TYPE_PRO_FORMA_INVOICE) : ?>
	<?php $class = 'pro-forma';?>
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
                    'format' => 'M d,Y',
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
    <div class="col-md-2">
        <?php echo $form->field($model, 'type')->hiddenInput()->label(false); ?>
        <?php echo Html::submitButton(Yii::t('backend', 'Go'), ['class' => $class . '-search btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>
