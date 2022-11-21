<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Item */

$this->title = 'Update Item';
$this->params['breadcrumbs'][] = ['label' => 'Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="item-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
