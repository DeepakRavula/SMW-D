<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CalendarEventColor */

$this->title = 'Create Calendar Event Color';
$this->params['breadcrumbs'][] = ['label' => 'Calendar Event Colors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calendar-event-color-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
