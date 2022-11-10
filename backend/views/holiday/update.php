<?php


/* @var $this yii\web\View */
/* @var $model common\models\Holiday */

$this->title = 'Edit Holiday';
$this->params['breadcrumbs'][] = ['label' => 'Holidays', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="holiday-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
