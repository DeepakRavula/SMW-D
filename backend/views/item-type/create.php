<?php


/* @var $this yii\web\View */
/* @var $model common\models\ItemType */

$this->title = 'Create Item Type';
$this->params['breadcrumbs'][] = ['label' => 'Item Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-type-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
