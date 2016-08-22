<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TaxTaxstatus */

$this->title = 'Update Tax Taxstatus: ' . ' ' . $model->int;
$this->params['breadcrumbs'][] = ['label' => 'Tax Taxstatuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->int, 'url' => ['view', 'id' => $model->int]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tax-taxstatus-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
