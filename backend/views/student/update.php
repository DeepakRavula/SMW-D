<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Student */

$this->title = 'Update Student';
$this->params['breadcrumbs'][] = ['label' => 'Students', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="student-update">

    <?php echo $this->render('_form', [
        'model' => $model,
        'customer' => $model->customer,
    ]) ?>

</div>