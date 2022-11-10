<?php


/* @var $this yii\web\View */
/* @var $model common\models\Tax */

$this->title = 'Add new Tax';
$this->params['breadcrumbs'][] = ['label' => 'Taxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="tax-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
