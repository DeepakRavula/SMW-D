<?php

use common\components\gridView\KartikGridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\User;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'title' => 'Recurring Payments',
        'withBorder' => true,
    ])
    ?>

<div class="clearfix"></div>
<?php Pjax::Begin(['id' => 'recurring-payment-list', 'timeout' => 6000, 'enablePushState' => false]); ?>
    <?= GridView::widget([
        'dataProvider' => $customerRecurringPaymentsDataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-condensed'],
        'columns' => [
            [
                'label' => 'Entry Day',
                'value' => function ($data) {
                    return $data->entryDay;
                }
            ],
            [
                'label' => 'Payment Day',
                'value' => function ($data) {
                    return  $data->paymentDay;
                }
            ],
            [
                'label' => 'Frequency',
                'value' => function ($data) {
                    return $data->paymentFrequencyId;
                },
            ],
            [
                'label' => 'Expiry Date',
                'value' => function ($data) {
                    return $data->expiryDate;
                },
            ],
            [
                'label' => 'Method',
                'value' => function ($data) {
                    return $data->paymentMethodId;
                },
            ],
            [
                'label' => 'amount',
                'value' => function ($data) {
                    return $data->amount;
                },
            ],
        ]
    ]); ?>
<?php Pjax::end(); ?>
<?php LteBox::end() ?>