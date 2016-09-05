<?php

use yii\helpers\Html;
use common\models\User;
use common\models\Invoice;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\jui\DatePicker;
use wbraganca\selectivity\SelectivityWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\bootstrap\ActiveForm */
$customer_id = (empty($customer->id)) ? null : (string)$customer->id;
?>


    <?php $form = ActiveForm::begin([
		'method' => 'get',
        'id' => 'customer-search-form',
	]); ?>

<div class="row">
<div class="col-md-4">
    <?php $customers = ArrayHelper::map(User::find()
        ->join('INNER JOIN','user_location','user_location.user_id = user.id')
        ->join('INNER JOIN','rbac_auth_assignment','rbac_auth_assignment.user_id = user.id')
        ->where(['user_location.location_id' => Yii::$app->session->get('location_id'),'rbac_auth_assignment.item_name' => 'customer'])			
        ->all(),'id','userProfile.fullName' );
    ?>
    <?= $form->field($userProfile, 'firstname');?>
</div>
</div>
<?php ActiveForm::end(); ?>
<script>
$(document).ready(function() {
    $('#customer-search-form').on('change','#invoice-customer_id',  function(){ $('#customer-search-form').submit(); });
});
</script>