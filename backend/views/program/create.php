<?php


/* @var $this yii\web\View */
/* @var $model common\models\Program */

$this->title = 'Add new Program';
$this->params['breadcrumbs'][] = ['label' => 'Programs', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="program-create p-10">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
