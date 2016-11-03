<?php


/* @var $this yii\web\View */
/* @var $model common\models\Course */

$this->title = 'Create Course';
$this->params['breadcrumbs'][] = ['label' => 'Courses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-create">

    <?php echo $this->render('_form', [
        'model' => $model,
        'teacher' => $teacher,
    ]) ?>

</div>
