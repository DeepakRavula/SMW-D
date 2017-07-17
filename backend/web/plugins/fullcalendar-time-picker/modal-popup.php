<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;

?>
<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
<script type="text/javascript" src="/admin/plugins/fullcalendar-time-picker/fullcalendar-time-picker.js"></script>
<?php
    Modal::begin([
            'header' => '<h4 class="m-0">Choose Date, Day and Time</h4>',
            'id' => 'calendar-date-time-picker-modal',
    ]);
?>
<div id="calendar-date-time-picker-error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div class="row-fluid">
	<div id="calendar-date-time-picker" ></div>
</div>
 <div class="form-group">
	<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary calendar-date-time-picker-save', 'name' => 'button']) ?>
	<?= Html::a('Cancel', '#', ['class' => 'btn btn-default calendar-date-time-picker-cancel']);
	?>
	<div class="clearfix"></div>
</div>
<?php Modal::end(); ?>




