<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PaymentFrequency */

$this->title = 'Create Payment Frequency';
$this->params['breadcrumbs'][] = ['label' => 'Payment Frequencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-frequency-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
