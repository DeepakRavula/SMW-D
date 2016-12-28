<?php

use yii\bootstrap\Modal;
use common\models\InvoiceLineItem;

Modal::begin([
    'header' => '<h4 class="m-0">Add Invoice Line Items</h4>',
    'id' => 'invoice-line-item-modal',
]);
echo $this->render('_form-invoice-line-item', [
        'model' => new InvoiceLineItem(),
        'invoiceModel' => $invoiceModel,
]);
Modal::end();

Modal::begin([
    'header' => '<h4 class="m-0">Apply Discounts</h4>',
    'id' => 'apply-discount-modal',
]);
echo $this->render('_form-apply-discount', [
        'invoiceModel' => $invoiceModel,
]);
Modal::end();
