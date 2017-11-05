<?php

?>
<?php if((empty($model->lineItem) || $model->lineItem->isOtherLineItems()) && $model->isInvoice()) :?>
	<div class="dropdown">
		<i class="fa fa-angle-down fa-lg"></i>
		<div class="dropdown-content dropdown-menu-right">
			<a class="add-new-misc" href="#">Add Item</a>
			<?php if(!empty($model->lineItem) && ($model->lineItem->isOtherLineItems())) :?>
				<a class = "apply-discount" href="#">Add Discount</a>
<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
<script>
$(document).ready(function () {
	$(document).on('click', '.dropdown', function () {
        $('.dropdown-content').css({'display': 'block'});
    });    
    $("body *:not(.dropdown)").click(function() {
        $('.dropdown-content').css({'display': 'none'});
    });
});
</script>