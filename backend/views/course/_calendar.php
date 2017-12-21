<?php

use common\models\LocationAvailability;
use kartik\depdrop\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup.php';
?>
<?php $this->render('/lesson/_color-code'); ?>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
 <div class="row-fluid">
	<div id="course-calendar">
    <div id="spinner" class="spinner" style="display:none;">
    <img src="/backend/web/img/loader.gif" alt="" height="50" width="50"/>
</div>
    </div>
</div>
<?php
    $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->language])->id;
    $minLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['fromTime' => SORT_ASC])
        ->one();
    $maxLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['toTime' => SORT_DESC])
        ->one();
    $minTime = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
    $maxTime = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>

<script>
    $(document).on('click', '.course-calendar-icon', function() {
        var name = $(this).parent();
        var teacherId = $('#course-teacherid').val();
        var duration = $('#course-duration').val();
        var params = $.param({ id: teacherId });
        if (!$.isEmptyObject(teacherId) && !$.isEmptyObject(duration)) {
            $.ajax({
                url: '<?= Url::to(['teacher-availability/availability-with-events']); ?>?' + params,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    var options = {
                        name: name,
                        date: moment(new Date()),
                        duration: duration,
                        businessHours: response.availableHours,
                        minTime: '<?= $minTime; ?>',
                        maxTime: '<?= $maxTime; ?>',
                        eventUrl: '<?= Url::to(['teacher-availability/show-lesson-event']); ?>?lessonId=&teacherId=' + teacherId
                    };
                    $('#calendar-date-time-picker').calendarPicker(options);
                }
            });
            return false;
        }
    });

    $(document).on('change', '#course-teacherid', function() {
        $('.remove-item').click();
        $('.day').val('');
        $('.time').val('');
        return false;
    });

    $(document).on('after-date-set', function(event, params) {
        if (!$.isEmptyObject(params.date)) {
            params.name.find('.lesson-time').find('.time').val(moment(params.date).format('DD-MM-YYYY h:mm A'));
            params.name.find('.lesson-day').find('.day').val(moment(params.date).format('dddd'));
        }
    });
</script>
