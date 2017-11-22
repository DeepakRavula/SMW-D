<?php

use yii\helpers\Url;
use common\models\LocationAvailability;
require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup.php';

?>
<?= $this->render('/lesson/_color-code');?>
<?php
    $locationId = Yii::$app->session->get('location_id');
    $minLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['fromTime' => SORT_ASC])
        ->one();
    $maxLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['toTime' => SORT_DESC])
        ->one();
    $from_time = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
    $to_time = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>
<script type="text/javascript">
    $(document).on('click', '.private-enrol-picker', function(){
        var teacherId = $('#course-teacherid').val();
        var duration = $('#courseschedule-duration').val();
        $.ajax({
            url: '<?= Url::to(['teacher-availability/availability-with-events']); ?>?id=' + teacherId,
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                var options = {
                    date: moment(new Date()),
                    duration: duration,
                    businessHours: response.availableHours,
                    minTime: '<?= $from_time; ?>',
                    maxTime: '<?= $to_time; ?>',
                    eventUrl: '<?= Url::to(['teacher-availability/show-lesson-event']); ?>?lessonId=&teacherId=' + teacherId
                };
                $('#calendar-date-time-picker').calendarPicker(options);
            }
        });
        return false;
    });
    
    $(document).on('after-date-set', function(event, params) {
        if (!$.isEmptyObject(params.date)) {
            $('#course-startdate').val(moment(params.date).format('DD-MM-YYYY h:mm A'));
            $('#courseschedule-day').val(moment(params.date).format('dddd'));
            $('#courseschedule-fromtime').val(moment(params.date).format('h:mm A'));
        }
    });
</script>
