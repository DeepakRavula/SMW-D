<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GroupEnrolment */

$this->title = 'Update Group Enrolment: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Group Enrolments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="group-enrolment-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
