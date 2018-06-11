<?php

use yii\widgets\Pjax;
use yii\helpers\Url;
use backend\models\search\InvoiceSearch;

?>
<?php Pjax::Begin(['id' => 'invoice-title']) ?>
<?php $title = 'Proforma Invoices';?>
<?php Pjax::end();?>