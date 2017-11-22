<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

Modal::begin([
    'header' => '<h4 class="m-0">Add Invoice Line Items</h4>',
    'id' => 'invoice-line-item-modal',
]); ?>
<div class="pull-right invoice-customer">
    <?= Html::label('Search'); ?>
    <?= Html::textInput('search', '', ['id' => 'item-search', 
        'url' => Url::to(['item/filter', 'invoiceId' => $invoiceModel->id])]); ?>
</div>
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
