<?php


/* @var $this yii\web\View */
/* @var $model common\models\TaxCode */

$this->title = 'Add new Tax Code';
$this->params['breadcrumbs'][] = ['label' => 'Tax Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="tax-code-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
