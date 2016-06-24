<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */

$this->title = 'Update Lesson';
$this->params['breadcrumbs'][] = ['label' => 'Lessons', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lesson-update p-20">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
