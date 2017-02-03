<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LocationDebt */

$this->title = 'Update Location Debt: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Location Debts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="location-debt-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
