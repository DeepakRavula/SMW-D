<?php

use backend\models\search\InvoiceSearch;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $invoice common\models\Invoice */

$this->title = (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? 'Add Pro-forma Invoice' : 'Add Invoice';
$this->params['breadcrumbs'][] = ['label' => (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? 'Pro-forma Invoice' : 'Invoice', 'url' => ['index', 'InvoiceSearch[type]' => $model->type]];
;
$this->params['breadcrumbs'][] = 'create';
?>
<div class="invoice-create p-20">

    <?php echo $this->render('_form', [
        'model' => $model,
		'unInvoicedLessonsDataProvider' => $unInvoicedLessonsDataProvider,
        'customer' => $customer,
    ]) ?>

</div>
