<?php


/* @var $this yii\web\View */
/* @var $model common\models\TeacherAvailability */

$this->title = 'Create Teacher Availability';
$this->params['breadcrumbs'][] = ['label' => 'Teacher Availabilities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-availability-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
