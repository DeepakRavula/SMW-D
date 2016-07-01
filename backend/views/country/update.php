<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Country */

$this->title = 'Update Country';
$this->params['breadcrumbs'][] = ['label' => 'Countries', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="country-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
