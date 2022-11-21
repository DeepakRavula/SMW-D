<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CalendarEventColor */

$this->title = 'Calendar Event Color';

?>
<div class="calendar-event-color-create">

    <?php echo $this->render('_form', [
        'eventModels' => $eventModels,
    ]) ?>

</div>
