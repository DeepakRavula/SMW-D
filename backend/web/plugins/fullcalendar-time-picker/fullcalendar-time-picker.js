$(document).on('click', '.calendar-date-time-picker-save', function () {
    if (!$.isEmptyObject($('#calendar-date-time-picker').fullCalendar('clientEvents', 'newEnrolment'))){
        $('#calendar-date-time-picker-modal').modal('hide');
    } else {
        $('#calendar-date-time-picker-error-notification').html('Please select a date time!').fadeIn().delay(5000).fadeOut();
    }
    return false;
});

$(document).on('change', '#calendar-date-time-picker-date', function () {
    var validationUrl = $(this).attr('validation-url');
    if (!$.isEmptyObject(validationUrl)) {
        $.ajax({
            url: validationUrl,
            type: 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function (response)
            {
                if (!$.isEmptyObject(response['lesson-date'])) {
                    $('#calendar-date-time-picker').fullCalendar('removeEvents', 'newEnrolment');
                    $('#calendar-date-time-picker-error-notification').html(response['lesson-date']).fadeIn().delay(5000).fadeOut();
                }
            }
        });
    }
    return false;
});

$(document).on('click', '.calendar-date-time-picker-cancel', function () {
    $('#calendar-date-time-picker-modal').modal('hide');
    return false;
});

$.fn.calendarPicker = function(options) {
    $('#calendar-date-time-picker-modal').modal('show');
    $('#calendar-date-time-picker-modal .modal-dialog').css({'width': '1000px'});
    $(document).on('shown.bs.modal', '#calendar-date-time-picker-modal', function () {
        calendar.showCalendar(options);
    });
};

var calendar = {
    showCalendar: function (calendarOptions) {
        $('#calendar-date-time-picker').fullCalendar('destroy');
        $('#calendar-date-time-picker').fullCalendar({
            defaultDate: moment(new Date()).format('YYYY-MM-DD'),
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'agendaWeek'
            },
            allDaySlot: false,
            height: 450,
            slotDuration: '00:15:00',
            titleFormat: 'DD-MMM-YYYY, dddd',
            defaultView: 'agendaWeek',
            minTime: calendarOptions.minTime,
            maxTime: calendarOptions.maxTime,
            selectConstraint: 'businessHours',
            eventConstraint: 'businessHours',
            businessHours: calendarOptions.businessHours,
            allowCalEventOverlap: true,
            overlapEventsSeparate: true,
            events: {
                url: calendarOptions.eventUrl,
                type: 'GET',
                error: function() {
                    $("#calendar-date-time-picker").fullCalendar("refetchEvents");
                }
            },
            select: function (start) {
                $('#calendar-date-time-picker').fullCalendar('removeEvents', 'newEnrolment');
                $('#calendar-date-time-picker-date').val(moment(start).format('DD-MM-YYYY h:mm A')).trigger('change');
                var endtime = start.clone();
                var durationMinutes = moment.duration(calendarOptions.duration).asMinutes();
                moment(endtime.add(durationMinutes, 'minutes'));
                $('#calendar-date-time-picker').fullCalendar('renderEvent',
                    {
                        id: 'newEnrolment',
                        start: start,
                        end: endtime,
                        allDay: false
                    },
                true // make the event "stick"
                );
                $('#calendar-date-time-picker').fullCalendar('unselect');
            },
            eventAfterAllRender: function () {
                $('.fc-short').removeClass('fc-short');
            },
            selectable: true,
            selectHelper: true
        });
    }
}

