<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use common\models\CustomerPaymentPreference;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

?>
<div class="col-md-12 p-b-20">
<h5><strong><?= 'Payment Preference' ?> </strong></h5> 
<?php 
    if (empty($model->customerPaymentPreference)) {
        echo Html::a('Add payment preference', null, ['class' => 'btn btn-success btn-sm', 'id' => 'payment-preference']);
    } else {
        
        $paymentPreferenceDataProvider = new ActiveDataProvider([
            'query' => CustomerPaymentPreference::find()
            ->where(['userId' => $model->id]),
        ]);
        
        $columns = [
            [
                'label' => 'Payment Method',
                'value' => function ($data) {
                    return $data->getPaymentMethodName();
                }
            ],
            'dayOfMonth'
        ];
        
        echo GridView::widget([
            'id' => 'payment-preference-grid',
            'dataProvider' => $paymentPreferenceDataProvider,
            'pjax' => true,
            'pjaxSettings' => [
                'neverTimeout' => true,
                'options' => [
                    'id' => 'payment-preference-listing',
                ],
            ],
            'columns' => $columns,
            'responsive' => false,
        ]); 
    }
    ?>
</div>
<?php
    Modal::begin([
        'header' => '<h4 class="m-0">Payment Preference</h4>',
        'id'=>'payment-preference-modal',
    ]);
?>
    <div id="payment-preference-content"></div>
<?php
    Modal::end();
?>

<?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
]) ?>
<div class="col-md-12 p-b-20">
<h5><strong><?= 'Accounts' ?> </strong></h5> 
<?php
echo GridView::widget([
'dataProvider' => $accountDataProvider,
'tableOptions' => ['class' => 'table table-bordered m-0'],
'headerRowOptions' => ['class' => 'bg-light-gray'],
'columns' => [
	 [
    'label' => 'Date',
    'value' => function ($data) {
        return Yii::$app->formatter->asDate($data->date);
    }
    ],
    [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'label' => 'Description',
        'value'=> function ($data) {
            return $data->getAccountType() . ' #' . $data->foreignKeyId. ' '
                . $data->getAccountActionType();
        }
    ],
    [
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'label' => 'Credit',
        'value' => function ($data) {
            return !empty($data->credit) ? Yii::$app->formatter->asCurrency($data->credit) : null;
        }
    ],
    [
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'label' => 'Debit',
        'value' => function ($data) {
        return !empty($data->debit) ? Yii::$app->formatter->asCurrency($data->debit) : null;
        }
    ],
    [
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'label' => 'Balance',
        'value' => function ($data) {
        	return !empty($data->balance) ? Yii::$app->formatter->asCurrency($data->balance) : '$0.00';
        }
    ],
   
],
]);
?>
</div>
<?php \yii\widgets\Pjax::end(); ?>

<script>
$(document).ready(function() {
    $(document).on('click', '#payment-preference', function (e) {
		modifyPaymentPreference();
  	});
    
    $(document).on('click', '#cancel', function (e) {
		$('#payment-preference-modal').modal('hide');
		return false;
  	});
});

$(document).on("click", "#payment-preference-grid tbody > tr", function() {
    modifyPaymentPreference();
});

function modifyPaymentPreference() {
    $.ajax({
        url    : '<?= Url::to(['customer-payment-preference/modify', 'id' => $model->id]); ?>',
        type   : 'post',
        dataType: "json",
        success: function(response)
        {
           if(response.status)
            {
                $('#payment-preference-content').html(response.data);
                $('#payment-preference-modal').modal('show');
            }
        }
    });
    return false;
}
</script>		