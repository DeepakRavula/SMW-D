<?php


/* @var $this yii\web\View */
/* @var $model common\models\Program */

$this->title = 'Edit Program';
$this->params['breadcrumbs'][] = ['label' => 'Programs', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="program-update p-10">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
