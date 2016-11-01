<?php


/* @var $this yii\web\View */
/* @var $model common\models\Qualification */

$this->title = 'Update Qualification: '.' '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Qualifications', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="qualification-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
