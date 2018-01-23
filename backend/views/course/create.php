<?php

require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup.php';
/* @var $this yii\web\View */
/* @var $model common\models\Course */

$this->title = 'Create Course';
$this->params['breadcrumbs'][] = ['label' => 'Courses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-create">

    <?php echo $this->render('_form', [
        'model' => $model,
        'courseSchedule' => $courseSchedule
    ]) ?>

</div>
