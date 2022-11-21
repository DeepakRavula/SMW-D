<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TeacherRoom */

$this->title = 'Update Teacher Room: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Teacher Rooms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="teacher-room-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
