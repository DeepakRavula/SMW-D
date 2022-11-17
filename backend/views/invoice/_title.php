<?php

use yii\widgets\Pjax;
use yii\helpers\Url;
use backend\models\search\InvoiceSearch;

?>
<?php Pjax::Begin(['id' => 'invoice-title']) ?>
<?php $title = (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? 'Proforma Invoices' : 'Invoices';?>
<a href="<?= Url::to(['index', 'InvoiceSearch[type]' => $model->type]);?>"><?= $title;?></a>  / 
<?= $model->getInvoiceNumber();?>
<?php Pjax::end();?>