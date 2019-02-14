<?php

use yii\helpers\Url;
use common\models\User;

?>
<?php if (!$model->isOpeningBalance() && !$model->isPaymentCreditInvoice()) : ?>
<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
<ul class="dropdown-menu dropdown-menu-right">
	<?php if (empty($model->user) || $model->user->isLocationWalkin()) : ?>
		<li><a class="add-customer" href="#">Add Existing Customer...</a></li>
		<li><a class="add-walkin" href="#">Add Walk-in With Name...</a></li>
	<?php elseif ($model->user->isCustomer()) : ?>
		<li><a class="add-customer" href="#">Change Customer...</a></li>
	<?php elseif ($model->user->isWalkin()) : ?>
		<li><a class="edit-walkin" href="#">Edit Walk-in...</a></li>
	<?php endif;?>
</ul>
<?php endif;?>

<script>
 	$(document).off('click', '.add-customer').on('click', '.add-customer', function () {
		$.ajax({
			url    : '<?= Url::to(['invoice/update-customer', 'id' => $model->id, "UserSearch[role_name]" => User::ROLE_CUSTOMER]); ?>',
			type   : 'get',
			dataType: 'json',
			success: function(response)
			{
				if (response.status) {
					$('#modal-content').html(response.data);
					$('#popup-modal').modal('show');
					$('#modal-save').hide();
					$('#popup-modal .modal-dialog').css({'width': '800px'});
					$('#popup-modal').find('.modal-header').html('<h4 class="m-0">Choose Customer</h4>');
				}
			}
		});
		return false;
	});

	$(document).off('click', '.edit-walkin').on('click', '.edit-walkin', function () {
		$.ajax({
            url    : '<?= Url::to(['invoice/edit-walkin', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: 'json',
            success: function(response)
            {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('#popup-modal .modal-dialog').css({'width': '400px'});
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Walkin</h4>');
                    $('.modal-save').show();
                    $('.modal-save').text('Save');
                }
            }
        });
        return false;
  	});
</script>