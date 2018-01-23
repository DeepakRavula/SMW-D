<?php 

use yii\helpers\Url;

?>

    <button type="button" style="margin-left:10px" class="close modal-close-button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
    <h4 class="m-0 pull-left">Add Line Items</h4>
    <div style="float:right; width: 150px" class="input-group input-group-sm">
        <input type="text" name="q" id="item-search" url= "<?= Url::to(['item/filter', 'invoiceId' => $invoiceModel->id]); ?>" 
               class="form-control pull-right" placeholder="Search">
        <div class="input-group-btn">
            <button type="submit" id="item-picker-submit" class="btn btn-default"><i class="fa fa-search"></i></button>
        </div>
    </div>
