<?php


/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethods */

$this->title = 'Create Payment Methods';
$this->params['breadcrumbs'][] = ['label' => 'Payment Methods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-methods-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
