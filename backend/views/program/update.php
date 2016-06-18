<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Program */

$this->title = 'Update Program: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Programs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="program-update p-10">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
