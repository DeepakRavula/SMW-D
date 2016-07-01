<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = 'Add new Location';
$this->params['breadcrumbs'][] = ['label' => 'Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="location-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
