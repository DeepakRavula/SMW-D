
<?php

use yii\helpers\ArrayHelper;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use common\models\Note;
use yii\helpers\Url;
use common\models\TeacherRoom;
use yii\bootstrap\Modal;
use backend\models\UserForm;

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
<script>
	$('.add-address').bind('click', function () {
		$('.address-fields').show();
		$('.hr-ad').hide();
		setTimeout(function () {
			$('.add-address').addClass('add-item');
		}, 100);
	});
	
	$('.add-phone').bind('click', function () {
		$('.phone-fields').show();
		$('.hr-ph').hide();
		setTimeout(function () {
			$('.add-phone').addClass('add-item-phone');
		}, 100);
	});
$(document).ready(function(){
	$(document).on('click', '.user-edit-button', function () {
        $('#user-edit-modal').modal('show');
        return false;
    });
	$(document).on('click', '.phone-cancel-btn', function () {
        $('#edit-phone-modal').modal('hide');
        return false;
    });
	$(document).on('click', '.user-phone-btn', function () {
		$.ajax({
            url    : '<?= Url::to(['/user/default/edit-phone', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#phone-content').html(response.data);
                    $('#edit-phone-modal').modal('show');
                	$('#edit-phone-modal .modal-dialog').css({'width': '800px'});
                }
            }
        });
        return false;
    });
	$(document).on('click', '.address-cancel-btn', function () {
        $('#edit-address-modal').modal('hide');
        return false;
    });
	$(document).on('click', '.user-address-btn', function () {
		$.ajax({
            url    : '<?= Url::to(['/user/default/edit-address', 'id' => $model->id]); ?>',
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
	$(document).on('beforeSubmit', '#address-form', function () {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status) {
        			$('#edit-address-modal').modal('hide');
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
        			$.pjax.reload({container:"#user-profile",replace:false,  timeout: 4000});
                    
                } else {
                    $('#error-notification').html(response.errors).fadeIn().delay(5000).fadeOut();
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
        			$('#edit-phone-modal').modal('hide');
        			$.pjax.reload({container:"#user-phone",replace:false,  timeout: 4000});
                    
                } else {
					$('#phone-form').yiiActiveForm('updateMessages', response.errors
					, true);
				}
            }
        });
        return false;
    });
});
</script>
