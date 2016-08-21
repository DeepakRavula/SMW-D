<?php

use yii\bootstrap\Modal;
use common\models\InvoiceLineItem;

Modal::begin([
    'header' => '<h3 class="m-0">Add Invoice Line Items</h3>',
    'id'=>'invoice-line-item-modal',
]);
echo $this->render('_form-invoice-line-item', [
		'model' => new InvoiceLineItem(),
]);
Modal::end();
?>