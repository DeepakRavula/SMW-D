<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\search\InvoiceSearch;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? 'Pro-forma Invoice' : 'Invoice';
$this->params['subtitle'] = Html::a('<i class="fa fa-pencil"></i> Edit', ['update', 'id' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title. '#' .$model->id;
?>
<style>
  .invoice-view .logo>img{
    width: 216px;
  }
    table>thead>tr>th:first-child,
    table>tbody>tr>td:first-child{
        text-align: left !important;
    }
    table>thead>tr>th:last-child,
    table>tbody>tr>td:last-child{
      text-align: right;
    }
    .badge{
      border-radius: 50px;
      font-size: 18px;
      font-weight: 400;
      padding: 5px 15px;
    }
</style>
<div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$invoiceContent =  $this->render('_view-invoice', [
    'model' => $model,
    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
]);

$paymentContent =  $this->render('_payment', [
    'model' => $model,
    'invoicePayments' => $invoicePayments
]);

?>
<?php echo Tabs::widget([
    'items' => [
		[
            'label' => 'Invoice',
            'content' => $invoiceContent,
			'active' => true,
			'options' => [
                    'id' => 'invoice',
            	],
        ],
		[
            'label' => 'Payments',
            'content' => $paymentContent,
			'options' => [
                    'id' => 'payment',
            	],
        ],
    ],
]);?>
</div>
</div>