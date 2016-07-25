<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Allocation */

$this->title = 'Update Allocation: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Allocations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="allocation-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
