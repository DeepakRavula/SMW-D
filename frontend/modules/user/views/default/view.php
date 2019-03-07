
<?php

use yii\helpers\Url;
use yii\bootstrap\Modal;
use backend\models\UserForm;
use common\models\UserEmail;
use common\models\UserContact;
use common\models\UserPhone;
use common\models\UserAddress;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->publicIdentity;
?>
<div class="row">
	<div class="col-md-6 user-detail">	
		<?php
        echo $this->render('_profile', [
            'model' => $model,
        ]);
        ?>
		<?= $this->render('_address', [
            'model' => $model,
        ]);
        ?>
	</div> 
	<div class="col-md-5 user-detail">	
		<?php
        echo $this->render('_phone', [
            'model' => $model,
        ]);
        ?>
		 <?php
        echo $this->render('_email', [
            'model' => $model,
        ]);
        ?>
	</div> 
</div>
<?php if ($model->isTeacher()):?>
<div class="row">
<div class="col-md-12">	
<?= $this->render('teacher/_cost-time-voucher-content', [
			'model' => $model,
			'searchModel' => $invoiceSearchModel,
			'invoicedLessonsDataProvider' => $invoicedLessonsDataProvider,
        ]);
    ?>
</div>
</div>
<?php endif;?>
<?php $userForm = new UserForm();
    $userForm->setModel($model);?>
<?php Modal::begin([
    'header' => '<h4 class="m-0"> Edit</h4>',
    'id' => 'user-edit-modal',
]); ?>
<?= $this->render('update/_profile', [
    'model' => $userForm,
    'userProfile' => $model->userProfile,
]);?>
<?php Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Email</h4>',
    'id' => 'email-modal',
]); ?>
<div id="email-content"></div>
<?php Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Phone</h4>',
    'id' => 'phone-modal',
]); ?>
<div id="phone-content"></div>
<?php Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Address</h4>',
    'id' => 'address-modal',
]); ?>
<div id="address-content"></div>
<?php Modal::end(); ?>
<script src="/plugins/bootbox/bootbox.min.js"></script>
<script>
	var contactTypes = {
		'email' : 1,
		'phone' : 2,
		'address' : 3,
	};
	var contact = {
        updatePrimary :function(event, val, form, data) {
			var target = event.currentTarget;
			var contactId = $(target).find('li:first').find('.contact').val();
			var id = '<?= $model->id;?>';
			var contactType = $(target).find('li:first').find('.contactType').val();
			var params = $.param({'id':id, 'contactId' : contactId, 'contactType' : contactType});
            $.ajax({
                url: "<?php echo Url::to(['user-contact/update-primary']);?>?" + params,
                type: "POST",
                dataType: "json",
                success: function (response)
                {
					if(response) {
						if(contactType == contactTypes.email) {
	                    	$.pjax.reload({container : '#user-email', timeout : 6000, async : true});
						} else if (contactType == contactTypes.phone) {
    						$.pjax.reload({container : '#user-phone', timeout : 6000, async : true});
						} else {
    						$.pjax.reload({container : '#user-address', timeout : 6000, async : true});
						};
					};
                }
            });
            return true;
        }
    };
$(document).ready(function(){
	$.fn.modal.Constructor.prototype.enforceFocus = function() {};
	$(document).on('click', '.address-cancel-btn', function () {
		$('#address-modal').modal('hide');
        return false;
	});
	$(document).on('click', '.phone-cancel-btn', function () {
		$('#phone-modal').modal('hide');
        return false;
	});
	$(document).on('click', '.user-edit-button', function () {
        $('#user-edit-modal').modal('show');
        return false;
    });
    $(document).on('click', '.email-cancel-btn', function () {
        $('#email-modal').modal('hide');
        return false;
    });
	$(document).on('beforeSubmit', '#address-form', function () {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status) {
        			$('#address-modal').modal('hide');
        			$.pjax.reload({container:"#user-address",replace:false,  timeout: 4000});
                    
                } else {
					$('#address-form').yiiActiveForm('updateMessages', response.errors
					, true);
				}
            }
        });
        return false;
    });
	$(document).on('click', '#user-cancel-btn', function () {
        $('#user-edit-modal').modal('hide');
        return false;
    });
	$(document).on('beforeSubmit', '#user-update-form', function () {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status) {
        			$('#user-edit-modal').modal('hide');
        			$.pjax.reload({container:"#user-profile",replace:false,  timeout: 6000}).done(function () {
    					$.pjax.reload({container: '#user-header', timeout: 6000});
					});
                } else {
                  $('#user-update-form').yiiActiveForm('updateMessages', response.errors, true); 
                }
            }
        });
        return false;
    });

    $(document).on('beforeSubmit', '#email-form', function () {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status) {
        			$('#email-modal').modal('hide');
        			$.pjax.reload({container:"#user-email",replace:false,  timeout: 4000});
                    
                } else {
					$('#email-form').yiiActiveForm('updateMessages', response.errors
					, true);
				}
            }
        });
        return false;
    });
	$(document).on('beforeSubmit', '#phone-form', function () {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status) {
        			$('#phone-modal').modal('hide');
        			$.pjax.reload({container:"#user-phone",replace:false,  timeout: 6000});
                } else {
					$('#phone-form').yiiActiveForm('updateMessages', response.errors
					, true);
				}
            }
        });
        return false;
    });
    $(document).on('click', '.user-contact-delete', function () {
		var contactId = $(this).attr('id') ;
		 bootbox.confirm({ 
  			message: "Are you sure you want to delete?", 
  			callback: function(result){
				if(result) {
					$('.bootbox').modal('hide');
				$.ajax({
					url: '<?= Url::to(['user-contact/delete']); ?>?id=' + contactId,
					type: 'post',
					success: function (response)
					{
						if (response.status)
						{
							if(response.type == contactTypes.email) {
	                    		$('#email-modal').modal('hide');
                            	$.pjax.reload({container: '#user-email', timeout: 6000});
							} else if (response.type == contactTypes.phone) {
        						$('#phone-modal').modal('hide');
								$.pjax.reload({container: '#user-phone', timeout: 6000});
							} else {
								$('#address-modal').modal('hide');
								$.pjax.reload({container: '#user-address', timeout: 6000});
							};
						} 
					}
				});
				return false;	
			}
			}
		});	
		return false;
        });
	$(document).on('click', '.add-email, .user-email-edit', function () {
 		var userId = '<?= $model->id;?>';
 		var contactId = $(this).attr('id') ;
 		if (contactId === undefined) {
 			var customUrl = '<?= Url::to(['user-contact/create-email']); ?>?id=' + userId;
 		} else {
 			var customUrl = '<?= Url::to(['user-contact/edit-email']); ?>?id=' + contactId;
 		}
 	  	$.ajax({
 			url    : customUrl,
 			type   : 'post',
 			dataType: "json",
 			data   : $(this).serialize(),
 			success: function(response)
 			{
 				if(response.status)
 				{
 					$('#email-content').html(response.data);
 			        $('#email-modal .modal-dialog').css({'width': '400px'});
 					$('#email-modal').modal('show');
 				}
 			}
 		});
 		return false;
  	});

 	$(document).on('click', '.add-address-btn, .user-address-edit', function () {
 		var userId = '<?= $model->id;?>';
  		var contactId = $(this).attr('id') ;
 
 		if (contactId === undefined) {
 			var customUrl = '<?= Url::to(['user-contact/create-address']); ?>?id=' + userId;
 		} else {
 			var customUrl = '<?= Url::to(['user-contact/edit-address']); ?>?id=' + contactId;
 		}
 	  	$.ajax({
 			url    : customUrl,
 			type   : 'post',
 			dataType: "json",
 			data   : $(this).serialize(),
 			success: function(response)
 			{
 				if(response.status)
 				{
 					$('#address-content').html(response.data);
 			        $('#address-modal .modal-dialog').css({'width': '400px'});
 					$('#address-modal').modal('show');
 				}
 			}
 		});
  		return false;
  	});

 	$(document).on('click', '.add-phone-btn, .user-phone-edit', function () {
 		var userId = '<?= $model->id;?>';
 		var contactId = $(this).attr('id') ;
 		if (contactId === undefined) {
 			var customUrl = '<?= Url::to(['user-contact/create-phone']); ?>?id=' + userId;
 		} else {
 			var customUrl = '<?= Url::to(['user-contact/edit-phone']); ?>?id=' + contactId;
 		}
 	  	$.ajax({
 			url    : customUrl,
 			type   : 'post',
 			dataType: "json",
 			data   : $(this).serialize(),
 			success: function(response)
 			{
 				if(response.status)
 				{
 					$('#phone-content').html(response.data);
 			        $('#phone-modal .modal-dialog').css({'width': '400px'});
 					$('#phone-modal').modal('show');
 				}
 			}
 		});
 		return false;
  	});
});
</script>