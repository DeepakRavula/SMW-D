<?php

use yii\widgets\Pjax;
use yii\helpers\Url;
use backend\models\search\InvoiceSearch;

?>

<?php $title = 'Proforma Invoices';?>
<a href="<?= Url::to(['index']);?>"><?= $title;?></a>/ <?= $model->getProformaInvoiceNumber();?>
