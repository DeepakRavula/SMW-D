<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = 'Update Location';
$this->params['breadcrumbs'][] = ['label' => 'Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="location-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
