<?php


/* @var $this yii\web\View */
/* @var $model common\models\City */

$this->title = 'Add new City';
$this->params['breadcrumbs'][] = ['label' => 'Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="city-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
