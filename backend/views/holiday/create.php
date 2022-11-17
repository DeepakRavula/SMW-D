<?php


/* @var $this yii\web\View */
/* @var $model common\models\Holiday */

$this->title = 'Add new Holiday';
$this->params['breadcrumbs'][] = ['label' => 'Holidays', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="holiday-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
