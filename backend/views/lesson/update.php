<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */

$this->title = 'Edit Lesson';
$this->params['breadcrumbs'][] = ['label' => 'Lessons', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="lesson-update p-20">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
