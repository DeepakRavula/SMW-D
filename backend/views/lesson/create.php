<?php


/* @var $this yii\web\View */
/* @var $model common\models\Lesson */

$this->title = 'Create Lesson';
$this->params['breadcrumbs'][] = ['label' => 'Lessons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
