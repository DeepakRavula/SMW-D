<?php


/* @var $this yii\web\View */
/* @var $model common\models\Province */

$this->title = 'Add new Province';
$this->params['breadcrumbs'][] = ['label' => 'Provinces', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="province-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
