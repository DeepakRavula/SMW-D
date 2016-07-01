<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Province */

$this->title = 'Update Province';
$this->params['breadcrumbs'][] = ['label' => 'Provinces', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="province-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
