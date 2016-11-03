<?php


/* @var $this yii\web\View */
/* @var $model common\models\Tax */

$this->title = 'Edit Tax';
$this->params['breadcrumbs'][] = ['label' => 'Taxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="tax-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
