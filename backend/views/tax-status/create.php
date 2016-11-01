<?php


/* @var $this yii\web\View */
/* @var $model common\models\TaxStatus */

$this->title = 'Create Tax Status';
$this->params['breadcrumbs'][] = ['label' => 'Tax Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-status-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
