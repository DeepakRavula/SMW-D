<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
<ul class="dropdown-menu dropdown-menu-right">
	<?php if(empty($model->user)) : ?>
		<li><a class="add-customer" href="#">Add Existing Customer...</a></li>
		<li><a class="add-walkin" href="#">Add Walk-in...</a></li>
	<?php elseif($model->user->isCustomer()) : ?>
		<li><a class="add-customer" href="#">Change Customer...</a></li>
	<?php elseif($model->user->isWalkin()) : ?>
		<li><a class="add-walkin" href="#">Edit Walk-in...</a></li>
	<?php endif;?>
</ul>
