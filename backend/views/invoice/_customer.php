<?php

use yii\helpers\Html;
use common\models\User;
use common\models\Invoice;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use wbraganca\selectivity\SelectivityWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\bootstrap\ActiveForm */
$customer_id = (empty($customer->id)) ? null : (string) $customer->id;
?>


    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'id' => 'customer-search-form',
    ]); ?>

<div class="col-md-12 p-0">
<div class="col-md-4">
    <?php $customers = ArrayHelper::map(User::find()
        ->join('INNER JOIN', 'user_location', 'user_location.user_id = user.id')
        ->join('INNER JOIN', 'rbac_auth_assignment', 'rbac_auth_assignment.user_id = user.id')
        ->where(['user_location.location_id' => Yii::$app->session->get('location_id'), 'rbac_auth_assignment.item_name' => 'customer'])
        ->all(), 'id', 'userProfile.fullName');
    ?>
    <?=
        $form->field($model, 'customer_id')->widget(SelectivityWidget::classname(), [
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => false,
                'items' => $customers,
                'value' => (empty($customer->id)) ? null : (string) $customer->id,
                'placeholder' => 'Select Customer',
            ],
        ]);
    ?>
</div>
<?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary pull-left m-r-20', 'name' => 'customer-invoice']) ?>
</div>
<?php ActiveForm::end(); ?>