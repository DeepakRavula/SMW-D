<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;

?>

<?php Pjax::Begin(['id' => 'invoice-view-payment-tab', 'timeout' => 6000]); ?> 

    <?php LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => false,
        'title' => 'Payments',
        'withBorder' => true,
    ]) ?>

        <?= $this->render('/invoice/payment/_payment-list', [
            'model' => $model,
            'searchModel' => $searchModel,
            'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
        ]); ?>

    <?php LteBox::end() ?>

<?php Pjax::end(); ?>

