<?php

use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Invoice;
?>
<?= Html::a(
    '<i title="Add" class="fa fa-plus-circle"></i>',
        ['invoice/blank-invoice', 'Invoice[customer_id]' => $userModel->id,
            'Invoice[type]' => INVOICE::TYPE_INVOICE, ],
    [
           'class' => 'add-new-invoice text-add-new m-r-10',
        ]
); ?>
	<?= Html::a('<i title="Print" class="fa fa-print"></i>', ['print/customer-invoice', 'id' => $userModel->id], ['id' => 'invoice-print', 'class' => 'text-add-new', 'target' => '_blank']) ?> 