<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProfessionalDevelopmentDay */

$this->title = 'Update Professional Development Day: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Professional Development Days', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="professional-development-day-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
