<?php

use yii\bootstrap\Modal;

Modal::begin([
    'header' => $this->render('_add-item-header', ['invoiceModel' => $invoiceModel]),
    'id' => 'invoice-line-item-modal',
    'closeButton' => false, 
]); ?>
<div id="item-spinner" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>
<div id="item-list-content">
<?= $this->render('_form-invoice-line-item', [
        'invoiceModel' => $invoiceModel,
        'itemDataProvider' => $itemDataProvider
]); ?>
</div>
<?php 
Modal::end();

Modal::begin([
    'header' => '<h4 class="m-0">Apply Discounts</h4>',
    'id' => 'apply-discount-modal',
]);
echo $this->render('_form-apply-discount', [
        'invoiceModel' => $invoiceModel,
]);
Modal::end();
