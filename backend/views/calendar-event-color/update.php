<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CalendarEventColor */

$this->title = 'Update Calendar Event Color: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Calendar Event Colors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="calendar-event-color-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
