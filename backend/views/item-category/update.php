<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ItemCategory */

$this->title = 'Update Item Category: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Item Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="item-category-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
