<?php
use yii\helpers\Url;
use backend\models\search\InvoiceSearch;
?>
<?php $title = (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? 'Proforma Invoices' : 'Invoices';?>
<a href="<?= Url::to(['index', 'InvoiceSearch[type]' => $model->type]);?>"><?= $title;?></a>  / 
<?= $model->getInvoiceNumber();?>