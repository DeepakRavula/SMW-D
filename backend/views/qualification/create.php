<?php


/* @var $this yii\web\View */
/* @var $model common\models\Qualification */

$this->title = 'Create Qualification';
$this->params['breadcrumbs'][] = ['label' => 'Qualifications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qualification-create">

    <?php echo $this->render('_form-create', [
        'model' => $model,
    ]) ?>

</div>
