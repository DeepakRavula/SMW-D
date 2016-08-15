<?php
Modal::begin([
    'header' => '<h2>Add Invoice Line Items</h2>',
    'id'=>'invoice-line-item-modal',
    'toggleButton' => ['label' => 'click me', 'class' => 'hide'],
]);
$this->render('_form-invoice-line-item', [
		'model' => new InvoiceLineItem(),
]);
Modal::end();
?>