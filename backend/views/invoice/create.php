<?php

use backend\models\search\InvoiceSearch;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $invoice common\models\Invoice */

$this->title = (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? 'Add Pro-forma Invoice' : 'Add Invoice';
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-create p-20">

    <?php echo $this->render('_form', [
        'model' => $model,
		'unInvoicedLessonsDataProvider' => $unInvoicedLessonsDataProvider,
    ]) ?>

</div>
