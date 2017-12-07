<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
<ul class="dropdown-menu dropdown-menu-right">
    <?php if(!$model->lineItem) :?>
        <li><a class="add-new-misc" href="#">Add Item...</a></li>
        <li><a class="edit-tax multiselect-disable" href="#">Edit Tax...</a></li>
        <li><a class = "apply-discount multiselect-disable" href="#">Edit Discount...</a></li>        
    <?php endif; ?>
    <?php if ($model->lineItem) : ?>
        <?php if ($model->lineItem->isOtherLineItems() && !$model->lineItem->isLessonItem()) : ?>
            <li><a class="add-new-misc" href="#">Add Item...</a></li>
            <li><a class="edit-tax" href="#">Edit Tax...</a></li>
        <?php else :?>
            <li><a class="add-new-misc multiselect-disable" href="#">Add Item...</a></li>
            <li><a class="edit-tax multiselect-disable" href="#">Edit Tax...</a></li> 
        <?php endif; ?>
        <li><a class = "apply-discount" href="#">Edit Discount...</a></li>
    <?php endif; ?>
</ul>