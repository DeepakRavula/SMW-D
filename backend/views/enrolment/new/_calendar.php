<?php

use yii\helpers\Url;
use common\models\LocationAvailability;
require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup.php';

/* @var $this yii\web\View */

?>
<?= $this->render('/lesson/_color-code');
?>
<style type="text/css">
.box-body .fc{
    margin:0 !important;
}
.apply-button {
	margin-top:25px;
}
#datepicker {
	margin-top:25px;
}
.ui-widget-content{
    font-size: 12px;
    line-height: 20px;
    overflow: inherit;
    color: #333333;
    padding: 10px;
    background-color: #ffffff;
    -webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    border-radius: 6px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box;
    background-image: none;
    text-transform: capitalize;
    position: absolute;
    top: -182px;
    width: 150px;
    border: none;
}
.ui-widget-content b{
	display:block;
	color:#ff0000;
	font-size:13px;
	font-weight:400;
	border-top:1px solid #ccc;
	padding-top:5px;
	padding-bottom:0;
}
.ui-widget-content b:first-child{
	padding:0;
	border:none;
}
.ui-widget-content:before{
	content:"";
	width: 0;
height: 0;
border-style: solid;
border-width: 10px 10px 0 10px;
border-color: #fff transparent transparent transparent;
position:absolute;
left:45%;
bottom:-10px;
}	
</style>
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
    $(document).on('click', '.enrolment-calendar-icon', function(){
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
            $('#course-startdate').val(moment(params.date).format('YYYY-MM-DD HH:mm:ss'));
            $('#courseschedule-day').val(moment(params.date).format('dddd'));
            var day = $('#courseschedule-day').val(); 
            var time = moment(params.date).format('h:mm A');
            var duration = $('#courseschedule-duration').val();
            $('.new-enrolment-time').text(day + ', ' + time + ' & ' + duration);
            $('#courseschedule-fromtime').val(time);
        }
    });
</script>
