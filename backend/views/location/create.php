<?php


/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = 'Add new Location';
$this->params['breadcrumbs'][] = ['label' => 'Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="location-create p-10">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
