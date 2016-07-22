<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Holiday */

$this->title = 'Update Holiday: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Holidays', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="holiday-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
