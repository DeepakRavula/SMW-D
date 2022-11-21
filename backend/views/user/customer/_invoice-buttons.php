<?php

use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Invoice;
?>

<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
    <ul class="dropdown-menu dropdown-menu-right" id="menu-shown">
	<li>
                <a href= "<?= Url::to(['invoice/blank-invoice', 'Invoice[customer_id]' => $userModel->id,
            'Invoice[type]' => INVOICE::TYPE_INVOICE, ]); ?>">
                    Add Invoice
                </a>
            </li>
	    <li>
                <a href= "<?= Url::to(['print/customer-invoice', 'id' => $userModel->id]); ?>">
                    Print
                </a>
            </li>
</ul>