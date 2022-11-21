<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TeacherRoom */

$this->title = 'Create Teacher Room';
$this->params['breadcrumbs'][] = ['label' => 'Teacher Rooms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-room-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
