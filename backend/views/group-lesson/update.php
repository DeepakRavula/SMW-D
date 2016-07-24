<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GroupLesson */

$this->title = 'Edit Group Lesson';
$this->params['breadcrumbs'][] = ['label' => 'Group Lessons', 'url' => ['group-course/view', 'id' => $model->groupCourse->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="group-lesson-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
