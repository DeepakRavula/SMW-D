<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GroupLesson */

$this->title = 'Update Group Lesson: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Group Lessons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="group-lesson-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
