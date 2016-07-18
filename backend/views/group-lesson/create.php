<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GroupLesson */

$this->title = 'Create Group Lesson';
$this->params['breadcrumbs'][] = ['label' => 'Group Lessons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-lesson-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
