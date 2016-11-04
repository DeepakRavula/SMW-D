<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Vacation */

$this->title = 'Update Vacation: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Vacations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="vacation-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
