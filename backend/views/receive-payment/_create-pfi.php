<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethods */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'id' => 'modal-form',
    'action' => Url::to(['proforma-invoice/create']),
]); ?>

    <div class="pull-right col-md-3">
        <label>Date Range To Filter Lessons</label>
        <?= DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'dateRange',
            'convertFormat' => true,
            'initRangeExpr' => true,
            'options' => [
                'class' => 'form-control',
                'readOnly' => true
            ],
            'pluginOptions' => [
                'autoApply' => true,
                'ranges' => [
                    Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["moment().startOf('day').subtract(6, 'days')", 'moment()'],
                    Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["moment().startOf('day').subtract(29, 'days')", 'moment()'],
                    Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')", "moment().endOf('month')"],
                    Yii::t('kvdrp', 'Last Month') => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                ],
                'locale' => [
                    'format' => 'M d,Y'
                ],
                'opens' => 'left'
            ]
        ]); ?>
    </div>

<?php ActiveForm::end(); ?>

    <div class = "row">
        <div class="col-md-12">
            <?= Html::label('Lessons', ['class' => 'admin-login']) ?>
            <?= $this->render('/receive-payment/_lesson-line-item', [
                'model' => $model,
                'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
                'searchModel' => $searchModel,
            ]);
            ?>
        </div>
    </div>
    <div class = "row">
        <div class="col-md-12">
            <?= Html::label('Invoices', ['class' => 'admin-login']) ?>
            <?= $this->render('/receive-payment/_invoice-line-item', [
                'model' => $model,
                'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                'searchModel' => $searchModel,
            ]);
            ?>
        </div>
    </div>

<script>
    var createPFI = {
        setAction: function() {
            var lessonIds = $('#lesson-line-item-grid').yiiGridView('getSelectedRows');
            var invoiceIds = $('#invoice-line-item-grid').yiiGridView('getSelectedRows');
            var params = $.param({ 'ProformaInvoice[lessonIds]': lessonIds, 'ProformaInvoice[invoiceIds]': invoiceIds });
            var url = '<?= Url::to(['proforma-invoice/create']) ?>?' + params;
            $('#modal-form').attr('action', url);
            return false;
        }
    };

    $(document).ready(function () {
        $('#popup-modal .modal-dialog').css({'width': '1000px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Create PFI</h4>');
        $('.modal-save').text('Create-PFI');
        $('.select-on-check-all').prop('checked', true);
        $('#invoice-line-item-grid .select-on-check-all').prop('disabled', true);
        $('#invoice-line-item-grid input[name="selection[]"]').prop('disabled', true);
        createPFI.setAction();
    });

    $(document).off('change', '#proformainvoice-daterange').on('change', '#proformainvoice-daterange', function () {
        $('#modal-spinner').show();
        var dateRange = $('#proformainvoice-daterange').val();
	    var lessonId = '<?= $model->lessonId ?>';
        var params = $.param({ 'ProformaInvoice[dateRange]': dateRange, 'ProformaInvoice[lessonId]' : lessonId });
        var url = '<?= Url::to(['proforma-invoice/create']) ?>?' + params;
	    $.pjax.reload({url:url, container: "#lesson-lineitem-listing", replace: false, async: false, timeout: 6000});
        $('.select-on-check-all').prop('checked', true);
        $('#modal-spinner').hide();
        createPFI.setAction();
        return false;
    });

    $(document).off('change', '#lesson-line-item-grid .select-on-check-all, input[name="selection[]"]').on('change', '#lesson-line-item-grid .select-on-check-all, input[name="selection[]"]', function () {
        createPFI.setAction();
        return false;
    });

    $(document).on('modal-success', function(event, params) {
        if (params.url) {
            window.location.href = params.url;
        }
        return false;
    });
</script>
