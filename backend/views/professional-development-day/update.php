<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProfessionalDevelopmentDay */

$this->title = 'Edit Professional Development Day';
$this->params['breadcrumbs'][] = ['label' => 'Professional Development Days', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="professional-development-day-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
