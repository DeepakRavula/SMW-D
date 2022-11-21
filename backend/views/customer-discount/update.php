<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerDiscount */

$this->title = 'Update Customer Discount: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Customer Discounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="customer-discount-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
