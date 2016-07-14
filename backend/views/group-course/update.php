<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */

$this->title = 'Update Group Course: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Group Courses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="group-course-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
