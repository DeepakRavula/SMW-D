<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProfessionalDevelopmentDay */

$this->title = 'Create Professional Development Day';
$this->params['breadcrumbs'][] = ['label' => 'Professional Development Days', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="professional-development-day-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
