<?php

use yii\helpers\Url;
?>

<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
 <div class="row-fluid">
	<div id="course-calendar">
    <div id="spinner" class="spinner" style="display:none;">
    <img src="/backend/web/img/loader.gif" alt="" height="50" width="50"/>
</div>
    </div>
</div>

<script>
    $(document).on('click', '.course-calendar-icon', function() {
        var name = $(this).parent();
        var teacherId = $('#course-teacherid').val();
        var duration = $('#course-duration').val();
        if (!$.isEmptyObject(teacherId) && !$.isEmptyObject(duration)) {
            var options = {
                name: name,
                date: moment(new Date()),
                durationId: '#course-duration',
                changeId: '#course-teacherid',
                teacherId: teacherId,
                parentPopUp: '#group-course-create-modal',
                eventUrl : '<?= Url::to(['teacher-availability/show-lesson-event']) ?>',
                availabilityUrl : '<?= Url::to(['teacher-availability/availability']) ?>'
            };
            $.fn.calendarPicker(options);
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
        $('#group-course-create-modal').modal('show');
    });
</script>
