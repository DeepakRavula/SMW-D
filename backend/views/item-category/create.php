<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ItemCategory */

$this->title = 'Create Item Category';
$this->params['breadcrumbs'][] = ['label' => 'Item Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-category-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
