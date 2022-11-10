<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerDiscount */

$this->title = 'Create Customer Discount';
$this->params['breadcrumbs'][] = ['label' => 'Customer Discounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-discount-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
