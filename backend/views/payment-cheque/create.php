<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PaymentCheque */

$this->title = 'Create Payment Cheque';
$this->params['breadcrumbs'][] = ['label' => 'Payment Cheques', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-cheque-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
