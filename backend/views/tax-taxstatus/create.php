<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TaxTaxstatus */

$this->title = 'Create Tax Taxstatus';
$this->params['breadcrumbs'][] = ['label' => 'Tax Taxstatuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-taxstatus-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
