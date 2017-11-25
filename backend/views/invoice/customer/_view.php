<?php

use yii\helpers\Html;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\select2\Select2Asset;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\GridView;

Select2Asset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div id="user-list-content">
	<?= $this->render('_list', [
		'userDataProvider' => $userDataProvider,
		'model' => $model
	]);?>
</div>
<script>
$(document).ready(function() {
	$(document).on('change keyup paste', '#invoice-username', function (e) {
		var userName = $(this).val();
		console.log(userName);
		var id = '<?= $model->id;?>';
		var params = $.param({'id' : id, 'userName' : userName});
		$.ajax({
            url    : '<?= Url::to(['invoice/fetch-user']); ?>?' + params,
            type   : 'get',
            dataType: 'json',
            success: function(response)
            {
               if(response.status) {
				   $('#user-list-content').html(response.data);
			   }
            }
        });
		return false;
	});
});
</script>