
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
		echo $this->render('list/_profile', [
			'model' => $model,
		]);
		?>
		<?= $this->render('list/_address', [
			'model' => $model,
		]);
		?>
	</div> 
	<div class="col-md-5 user-detail">	
		<?php
		echo $this->render('list/_phone', [
			'model' => $model,
		]);
		?>
		 <?php
		echo $this->render('list/_email', [
			'model' => $model,
		]);
		?>
	</div> 
</div>
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
    'header' => '<h4 class="m-0">Edit</h4>',
    'id' => 'edit-phone-modal',
]); ?>
<div id="phone-content"></div>
<?php Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit</h4>',
    'id' => 'edit-address-modal',
]); ?>
<div id="address-content"></div>
<?php Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit</h4>',
    'id' => 'edit-email-modal',
]); ?>
<div id="email-content"></div>
<?php Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Email</h4>',
    'id' => 'add-email-modal',
]); ?>
<?= $this->render('create/_email', [
	'emailModel' => new UserEmail(),
	'model' => new UserContact(),
	'userModel' => $model,
]);?>
<?php Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Add Phone</h4>',
    'id' => 'add-phone-modal',
]); ?>
<?= $this->render('create/_phone', [
	'phoneModel' => new UserPhone(),
	'model' => new UserContact(),
	'userModel' => $model,
]);?>
<?php Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Add Address</h4>',
    'id' => 'add-address-modal',
]); ?>
<?= $this->render('create/_address', [
	'addressModel' => new UserAddress(),
	'model' => new UserContact(),
	'userModel' => $model,
]);?>
<?php Modal::end(); ?>
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
	$(document).on('click', '.add-email', function () {
		$('#useremail-email').val('');
		$('#add-email-modal').modal('show');
        $('#add-email-modal .modal-dialog').css({'width': '400px'});
        return false;
	});
        $(document).on('click', '.user-email-edit', function () {
		var contactId = $(this).attr('id') ;
              $.ajax({
                url    : '<?= Url::to(['user-contact/edit-email']); ?>?id=' + contactId,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#add-email-modal .modal-body').html(response.data);
                         $('#add-email-modal .modal-dialog').css({'width': '400px'});
                        $('#add-email-modal').modal('show');
                    } else {
                        $('#email-form').yiiActiveForm('updateMessages',
                                response.errors
                                , true);
                    }
                }
            });
            
		return false;
	});
  	$(document).on('click', '.add-address-btn', function () {
                $('#useraddress-address').val('');
                $('#useraddress-cityid').val('');
                $('#useraddress-provinceid').val('');
                $('#useraddress-countryid').val('');
                $('#useraddress-postalcode').val('');
		$('#add-address-modal').modal('show');
       $('#add-address-modal .modal-dialog').css({'width': '500px'});
        return false;
	});
	$(document).on('click', '.address-cancel-btn', function () {
		$('#add-address-modal').modal('hide');
        return false;
	});
	$(document).on('click', '.add-phone-btn', function () {
                $('#userphone-number').val('');
                $("#userphone-extension").val('');
		$('#add-phone-modal').modal('show');
        $('#add-phone-modal .modal-dialog').css({'width': '400px'});
        return false;
	});
	$(document).on('click', '.phone-cancel-btn', function () {
		$('#add-phone-modal').modal('hide');
        return false;
	});
	$(document).on('click', '.user-edit-button', function () {
        $('#user-edit-modal').modal('show');
        return false;
    });
    $(document).on('click', '.email-cancel-btn', function () {
        $('#add-email-modal').modal('hide');
        return false;
    });
	$(document).on('click', '.user-address-btn', function () {
		$.ajax({
            url    : '<?= Url::to(['user/edit-address', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#address-content').html(response.data);
                    $('#edit-address-modal').modal('show');
                	$('#edit-address-modal .modal-dialog').css({'width': '800px'});
                }
            }
        });
        return false;
    });
	$(document).on('click', '.user-delete-button', function () {
		var id = '<?= $model->id;?>';
		 bootbox.confirm({ 
  			message: "Are you sure you want to delete this user?", 
  			callback: function(result){
				if(result) {
					$('.bootbox').modal('hide');
				$.ajax({
					url: '<?= Url::to(['user/delete']); ?>?id=' + id,
					type: 'post',
					success: function (response)
					{
						if (response.status)
						{
                            window.location.href = response.url;
						} else {
							$('#lesson-conflict').html(response.message).fadeIn().delay(5000).fadeOut();

						}
					}
				});
				return false;	
			}
			}
		});	
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
        			$('#add-address-modal').modal('hide');
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
        			$('#add-email-modal').modal('hide');
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
        			$('#add-phone-modal').modal('hide');
        			$.pjax.reload({container:"#user-phone",replace:false,  timeout: 6000});
                } else {
					$('#phone-form').yiiActiveForm('updateMessages', response.errors
					, true);
				}
            }
        });
        return false;
    });
       $(document).on('click', '.user-phone-edit', function () {
 		var contactId = $(this).attr('id') ;
               $.ajax({
                 url    : '<?= Url::to(['user-contact/edit-phone']); ?>?id=' + contactId,
                 type: 'get',
                 dataType: "json",
                 success: function (response)
                 {
                     if (response.status)
                     {
                         $('#add-phone-modal .modal-body').html(response.data);
                          $('#add-phone-modal .modal-dialog').css({'width': '400px'});
                         $('#add-phone-modal').modal('show');
                     } else {
                         $('#phone-form').yiiActiveForm('updateMessages',
                                 response.errors
                                 , true);
                     }
                 }
             });
 		return false;
 	});
            $(document).on('click', '.user-address-edit', function () {
 		var contactId = $(this).attr('id') ;
               $.ajax({
                 url    : '<?= Url::to(['user-contact/edit-address']); ?>?id=' + contactId,
                 type: 'get',
                 dataType: "json",
                 success: function (response)
                 {
                     if (response.status)
                     {
                         $('#add-address-modal .modal-body').html(response.data);
                          $('#add-address-modal .modal-dialog').css({'width': '400px'});
                         $('#add-address-modal').modal('show');
                     } else {
                         $('#address-form').yiiActiveForm('updateMessages',
                                 response.errors
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
	                    		$('#add-email-modal').modal('hide');
                            	$.pjax.reload({container: '#user-email', timeout: 6000});
							} else if (response.type == contactTypes.phone) {
								$('#add-phone-modal').modal('hide');
								$.pjax.reload({container: '#user-phone', timeout: 6000});
							} else {
								$('#add-address-modal').modal('hide');
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
});
</script>