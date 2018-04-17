<?php

use common\models\LocationAvailability;
use common\models\Location;
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
    $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
    $minLocationAvailability = LocationAvailability::find()
            ->location($locationId)
            ->locationaAvailabilityHours()
            ->orderBy(['fromTime' => SORT_ASC])
            ->one();
        $maxLocationAvailability = LocationAvailability::find()
            ->location($locationId)
            ->locationaAvailabilityHours()
            ->orderBy(['toTime' => SORT_DESC])
            ->one();
        if (empty($minLocationAvailability)) {
            $minTime = LocationAvailability::DEFAULT_FROM_TIME;
        } else {
            $minTime = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
        }
        if (empty($maxLocationAvailability)) {
            $maxTime = LocationAvailability::DEFAULT_TO_TIME;
        } else {
            $maxTime = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
        }
?>

<script>
    $(document).on('click', '.course-calendar-icon', function() {
        var name = $(this).parent();
        var teacherId = $('#course-teacherid').val();
        var duration = $('#course-duration').val();
        if (!$.isEmptyObject(teacherId) && !$.isEmptyObject(duration)) {
            var options = {
                name: name,
                date: moment(new Date()),
                duration: duration,
                selectConstraint: {
                    start: '00:01', // a start time (10am in this example)
                    end: '24:00', // an end time (6pm in this example)
                    dow: [ 1, 2, 3, 4, 5, 6, 0 ]
                },
                eventConstraint: {
                    start: '00:01', // a start time (10am in this example)
                    end: '24:00', // an end time (6pm in this example)
                    dow: [ 1, 2, 3, 4, 5, 6, 0 ]
                },
                teacherId: teacherId
            };
            $('#calendar-date-time-picker').calendarPicker(options);
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
            params.name.find('.lesson-time').find('.time').val(moment(params.date).format('MMM D,YYYY h:mm A'));
            params.name.find('.lesson-day').find('.day').val(moment(params.date).format('dddd'));
        }
    });
</script>
