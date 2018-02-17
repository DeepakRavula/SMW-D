<?php
use common\models\Payment;
use common\models\Invoice;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;

?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Add Payment</h4>',
    'id' => 'payment-modal',
]);
echo $this->render('payment-method/_form', [
    'model' => new Payment(),
    'invoice' => $model,
]);
Modal::end(); ?>

 <?php Pjax::Begin(['id' => 'invoice-view-payment-tab', 'timeout' => 6000]); ?> 
<?php $boxTools = null;?>
<?php $boxTools = '<i title="Add" class="fa fa-plus add-payment m-r-10"></i>' ?>
<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => $boxTools,
        'title' => 'Payments',
        'withBorder' => true,
    ])
    ?>


<div style="margin-bottom: 10px">   
<?= Html::a(Yii::t('backend', 'Apply Credit'), ['#'], ['class' => 'btn btn-primary btn-sm apply-credit']);?>
</div>
 
<?php
$columns = [
      [
        'contentOptions' => ['class' => 'text-left','style' => 'max-width:60px;'],
        'label' => 'Date',
        'value' => function ($data) {
            return Yii::$app->formatter->asDate($data->date);
        },
        ],
    [
        'contentOptions' => ['class' => 'text-left','style' => 'max-width:60px;'],
        'label' => 'Type',
        'value' => function ($data) {
            return $data->paymentMethod->name;
        },
        ],
    [
        'contentOptions' => ['class' => 'text-left', 'style' => 'max-width:60px;'],
        'label' => 'Ref',
        'value' => function ($data) {
            return $data->reference;
        },
        ],
    [
        'contentOptions' => ['class' => 'text-left','style' => 'max-width:155px;overflow: auto; word-wrap: break-word;'],
        'label' => 'Notes',
        'value' => function ($data) {
            return $data->notes;

        },
        ],
        [
            'label'=>'Amount',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right','style' => 'max-width:60px;'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->amount);
            },
        ],
    ]; ?>

<div>
	<?php yii\widgets\Pjax::begin([
        'id' => 'invoice-payment-listing',
        'timeout' => 6000,
    ]) ?>
	<?= GridView::widget([
        'id' => 'payment-grid',
        'dataProvider' => $invoicePaymentsDataProvider,
        'columns' => $columns,
    'summary' => false,
        'emptyText' => false,
        'options' => ['class' => 'col-md-12'],
    'tableOptions' => ['class' => 'table table-condensed'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    ]);
    ?>
<?php \yii\widgets\Pjax::end(); ?>	
</div>
<?php
    $amount = 0;
    if ($model->total > $model->invoicePaymentTotal) {
        $amount = $model->balance;
    }
?>
<?php if ((int) $model->type === Invoice::TYPE_INVOICE):?>
<div class="clearfix"></div>
<?php endif; ?>
<?php LteBox::end() ?>
<?php Pjax::end(); ?>
<script type="text/javascript">
    $(document).on('click', '#apply-credit-grid td', function () {
        var amount = $(this).closest('tr').data('amount');
        var id = $(this).closest('tr').data('id');
        var type = $(this).closest('tr').data('source');    
        var amountNeeded = <?= $amount; ?>; 
        if(amount > amountNeeded) {
            $('input[name="Payment[amount]"]').val((amountNeeded).toFixed(2));          
        } else {
            $('input[name="Payment[amount]"]').val((amount).toFixed(2));          
        }
        $('input[name="Payment[amountNeeded]"]').val((amountNeeded).toFixed(2));          
        $('#payment-credit').val((amount).toFixed(2));
        $('#payment-sourceid').val(id);
        $('#payment-sourcetype').val(type);
        return false;
    });
    
    $(document).on('beforeSubmit', '#apply-credit-form', function (e) {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: 'json',
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $.pjax.reload({container: "#invoice-view-lineitem-listing", replace:false,async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-view-payment-tab", replace:false,async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-bottom-summary", replace: false, async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-header-summary", replace: false, async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-user-history", replace: false, async: false, timeout: 6000});
                    $('#credit-modal').modal('hide');
                } else {
                    $('#apply-credit-form').yiiActiveForm('updateMessages', response.errors , true);
                }
            }
        });
        return false;
    });
</script>
