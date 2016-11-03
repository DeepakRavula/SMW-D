<?php


/* @var $this yii\web\View */
/* @var $model common\models\ItemType */

$this->title = 'Update Item Type: '.' '.$model->name;
$this->params['breadcrumbs'][] = ['label' => 'Item Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="item-type-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
