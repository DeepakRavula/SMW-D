<?php

?>
<?php if((empty($model->lineItem) || $model->lineItem->isOtherLineItems()) && $model->isInvoice()) :?>
        <i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu dropdown-menu-right">
			<?php if(empty($model->lineItem) || (!empty($model->lineItem) && $model->lineItem->isOtherLineItems() && !$model->lineItem->isLessonItem())) :?>
            	<li><a class="add-new-misc" href="#">Add Item...</a></li>
                <li><a class="edit-item" href="#">Edit Item...</a></li>
			<?php endif; ?>
			<?php if(!empty($model->lineItem) && $model->lineItem->isOtherLineItems() && !$model->lineItem->isLessonItem()) :?>
				<li><a class="edit-tax" href="#">Edit Tax...</a></li>
			<?php endif; ?>
			<?php if(!empty($model->lineItem) && $model->lineItem->isOtherLineItems()) :?>
				<li><a class = "apply-discount" href="#">Edit Discount...</a></li>
			<?php endif; ?>
        </ul>
<?php endif; ?>