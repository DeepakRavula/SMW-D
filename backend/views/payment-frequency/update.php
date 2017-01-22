<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentFrequency */

$this->title = 'Update Payment Frequency: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Payment Frequencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="payment-frequency-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
