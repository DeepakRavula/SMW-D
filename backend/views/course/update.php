<?php


/* @var $this yii\web\View */
/* @var $model common\models\Course */

$this->title = 'Edit GroupCourse';
?>
<div class="course-update">

    <?php echo $this->render('_form', [
        'model' => $model,
        'teacher' => $teacher,
    ]) ?>

</div>
