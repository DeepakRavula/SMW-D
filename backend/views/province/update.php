<?php


/* @var $this yii\web\View */
/* @var $model common\models\Province */

$this->title = 'Edit Province';
$this->params['breadcrumbs'][] = ['label' => 'Provinces', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="province-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
