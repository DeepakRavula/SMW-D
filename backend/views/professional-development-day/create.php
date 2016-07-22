<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProfessionalDevelopmentDay */

$this->title = 'Add new Professional Development Day';
$this->params['breadcrumbs'][] = ['label' => 'Professional Development Days', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="professional-development-day-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
