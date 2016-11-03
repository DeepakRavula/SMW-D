<?php


/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = 'Edit Invoice';
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="invoice-update">

    <?php echo $this->render('_update-form', [
        'model' => $model,
    ]) ?>

</div>
