<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Tax */

$this->title = 'Update Tax';
$this->params['breadcrumbs'][] = ['label' => 'Taxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tax-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
