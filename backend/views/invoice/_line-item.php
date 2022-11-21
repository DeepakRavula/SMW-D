<?php

use yii\bootstrap\Modal;
?>
<?php 


Modal::begin([
    'header' => '<h4 class="m-0">Edit Discounts</h4>',
    'id' => 'apply-discount-modal',
    'footer' => $this->render('_submit-button', [
        'deletable' => false,
        'saveClass' => 'apply-discount-form-save',
        'cancelClass' => 'invoice-apply-discount-cancel'
    ])
]); ?>
<div id="apply-discount-content"></div>
<?php Modal::end(); ?>
