<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */

$this->title = 'Update Enrolment: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Enrolments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="enrolment-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
