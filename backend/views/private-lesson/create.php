<?php


/* @var $this yii\web\View */
/* @var $model common\models\PrivateLesson */

$this->title = 'Create Private Lesson';
$this->params['breadcrumbs'][] = ['label' => 'Private Lessons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="private-lesson-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
