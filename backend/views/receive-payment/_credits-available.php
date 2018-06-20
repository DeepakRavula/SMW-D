<?php

use yii\widgets\Pjax;
use yii\Helpers\HTML;

?>
<?php Pjax::Begin(['id' => 'invoice-lineitem-listing', 'timeout' => 6000]); ?>
    <div class = "row">
        <div class = "col-md-12">
        <span class ="m-r-10"> <?=  Html::checkbox('apply-credit', true, ['label' => '', 'class' => 'apply-credit-checkbox']) ?>  <?=  Html::Label('Customer Credits
        ', 'customer-credit'); ?>  </span>
        <span class ="m-r-10 credits-available-amount" id="credit-available-amount-id" ><?= $creditsAvailable; ?> </span>
        </div>
    </div>
<?php Pjax::end(); ?>