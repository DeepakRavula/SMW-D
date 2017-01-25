<?php

use yii\helpers\Html;
use common\models\User;
use common\models\Invoice;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\bootstrap\ActiveForm */
$customer_id = (empty($customer->id)) ? null : (string) $customer->id;
?>

<div class="guest-form p-10">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'id' => 'customer-search-form',
    ]); ?>

    <div class="row">
        <?php $customers = ArrayHelper::map(User::find()
            ->join('INNER JOIN', 'user_location', 'user_location.user_id = user.id')
            ->join('INNER JOIN', 'rbac_auth_assignment', 'rbac_auth_assignment.user_id = user.id')
            ->where(['user_location.location_id' => Yii::$app->session->get('location_id'), 'rbac_auth_assignment.item_name' => 'customer'])
            ->all(), 'id', 'userProfile.fullName');
        ?>
        <div class="col-md-4">
            <?= $form->field($userModel, 'firstname'); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($userModel, 'lastname'); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($customer, 'email'); ?>
        </div>
        <div class="col-md-12">
            <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary m-r-20', 'name' => 'guest-invoice']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
