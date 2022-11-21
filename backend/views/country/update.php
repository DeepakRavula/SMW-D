<?php


/* @var $this yii\web\View */
/* @var $model common\models\Country */

$this->title = 'Edit Country';
$this->params['breadcrumbs'][] = ['label' => 'Countries', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="country-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
