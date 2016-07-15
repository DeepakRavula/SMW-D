<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */

$this->title = 'Edit Group Course';
$this->params['breadcrumbs'][] = ['label' => 'Group Courses', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="group-course-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
