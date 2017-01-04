<?php


/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = 'Edit Location';
$this->params['breadcrumbs'][] = ['label' => 'Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="location-update p-10">

    <?php echo $this->render('_form', [
        'model' => $model,
        'events' => $events,
    ]) ?>

</div>
