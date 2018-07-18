<?php

use yii\widgets\Pjax;
use yii\helpers\Url;
use backend\models\search\InvoiceSearch;

?>

<?php $title = 'Payment Requests';?>
<a href="<?= Url::to(['index']);?>"><?= $title;?></a> / <?= $model->getProformaInvoiceNumber();?>
