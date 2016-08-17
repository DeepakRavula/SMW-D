<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentCheque */

$this->title = 'Update Payment Cheque: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Payment Cheques', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="payment-cheque-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
