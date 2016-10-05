<?php

use yii\bootstrap\Modal;
use common\models\InvoiceLineItem;

Modal::begin([
    'header' => '<h4 class="m-0">Add Invoice Line Items</h4>',
    'id'=>'invoice-line-item-modal',
]);
echo $this->render('_form-invoice-line-item', [
		'model' => new InvoiceLineItem(),
		'invoiceModel' => $invoiceModel,
]);
Modal::end();
?>