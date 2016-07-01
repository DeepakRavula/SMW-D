<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\City */

$this->title = 'Update City';
$this->params['breadcrumbs'][] = ['label' => 'Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="city-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
