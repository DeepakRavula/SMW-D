<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */

$this->title = 'Add new Group Course';
$this->params['breadcrumbs'][] = ['label' => 'Group Courses', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="group-course-create">

    <?php echo $this->render('_form', [
        'model' => $model,
		'teacher' => $teacher,
    ]) ?>

</div>
