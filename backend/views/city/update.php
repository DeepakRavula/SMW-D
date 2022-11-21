<?php


/* @var $this yii\web\View */
/* @var $model common\models\City */

$this->title = 'Edit City';
$this->params['breadcrumbs'][] = ['label' => 'Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="city-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
