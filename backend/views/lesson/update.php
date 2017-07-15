<?php


/* @var $this yii\web\View */
/* @var $model common\models\Lesson */

$this->title = 'Update Lesson: '.' '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Lessons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<style>
	.user-details-wrapper{
        background: #F9F9F9 !important;
    }
</style>
<div class="lesson-update">
    <?php echo $this->render($view, $data); ?>

</div>
