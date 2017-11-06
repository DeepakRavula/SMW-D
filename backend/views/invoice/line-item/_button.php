<?php

?>
<?php if((empty($model->lineItem) || $model->lineItem->isOtherLineItems()) && $model->isInvoice()) :?>
        <i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a class="add-new-misc" href="#">Add Item...</a></li>
			<?php if(!empty($model->lineItem) && ($model->lineItem->isOtherLineItems())) :?>
				<li><a class = "apply-discount" href="#">Add Discount</a></li>
<?php endif; ?>
        </ul>
	
<?php endif; ?>