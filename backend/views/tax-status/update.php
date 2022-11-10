<?php


/* @var $this yii\web\View */
/* @var $model common\models\TaxStatus */

$this->title = 'Update Tax Status: '.' '.$model->name;
$this->params['breadcrumbs'][] = ['label' => 'Tax Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tax-status-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
