<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = 'Add Invoice';
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
