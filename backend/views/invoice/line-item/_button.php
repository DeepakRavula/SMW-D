<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
<ul class="dropdown-menu dropdown-menu-right">
    <?php if(empty($model->lineItem)) :?>
        <li><a class="add-new-misc" href="#">Add Item...</a></li>
    <?php else ?>
    <?php if ($model->lineItem->isOtherLineItems() && !$model->lineItem->isLessonItem()) : ?>
        <li><a class="add-new-misc" href="#">Add Item...</a></li>
        
        <?php if($model->lineItem->isOtherLineItems() && !$model->lineItem->isLessonItem()) :?>
            <li><a class="edit-tax" href="#">Edit Tax...</a></li>
        <?php endif; ?>
        <?php if($model->lineItem->isOtherLineItems()) :?>
            <li><a class = "apply-discount" href="#">Edit Discount...</a></li>
        <?php endif; ?>
    <?php endif; ?>
</ul>