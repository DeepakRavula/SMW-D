<?php


/* @var $this yii\web\View */
/* @var $model common\models\TeacherAvailability */

$this->title = 'Update Teacher Availability: '.' '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Teacher Availabilities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="teacher-availability-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
