<?php

use common\models\Invoice;
use backend\models\search\InvoiceSearch;
use yii\helpers\Html;
use yii\bootstrap\Tabs;

$this->title = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$indexInvoice =  $this->render('_index-invoices', [    
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
]);

?>

<?php echo Tabs::widget([
    'items' => [
		[
            'label' => 'Invoice',
            'content' => (int) $searchModel->type === Invoice::TYPE_INVOICE  ? $indexInvoice : null,
			'url'=>['/invoice/index','InvoiceSearch[type]' => Invoice::TYPE_INVOICE],
			'active' => (int) $searchModel->type === Invoice::TYPE_INVOICE ,
        ],
		[
            'label' => 'Pro-forma Invoice',
            'content' => (int) $searchModel->type === Invoice::TYPE_PRO_FORMA_INVOICE  ? $indexInvoice : null,
			'url'=>['/invoice/index','InvoiceSearch[type]' => Invoice::TYPE_PRO_FORMA_INVOICE ],
			'active' => (int) $searchModel->type === Invoice::TYPE_PRO_FORMA_INVOICE ,
        ],
    ],
]);?>
<div class="clearfix"></div>
</div>
</div>
