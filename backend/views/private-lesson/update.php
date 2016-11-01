<?php


/* @var $this yii\web\View */
/* @var $model common\models\PrivateLesson */

$this->title = 'Update Private Lesson: '.' '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Private Lessons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="private-lesson-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
