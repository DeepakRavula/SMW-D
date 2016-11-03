<?php


/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethods */

$this->title = 'Update Payment Methods: '.' '.$model->name;
$this->params['breadcrumbs'][] = ['label' => 'Payment Methods', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="payment-methods-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
