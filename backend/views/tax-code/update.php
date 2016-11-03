<?php


/* @var $this yii\web\View */
/* @var $model common\models\TaxCode */

$this->title = 'Update Tax Code: '.' '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tax Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tax-code-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
