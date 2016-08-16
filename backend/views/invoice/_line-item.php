<?php

use yii\bootstrap\Modal;
use common\models\InvoiceLineItem;

Modal::begin([
    'header' => '<h2>Add Invoice Line Items</h2>',
    'id'=>'invoice-line-item-modal',
]);
echo $this->render('_form-invoice-line-item', [
		'model' => new InvoiceLineItem(),
]);
Modal::end();
?>