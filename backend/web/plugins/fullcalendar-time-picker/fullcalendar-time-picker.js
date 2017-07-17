$(document).on('click', '.calendar-date-time-picker-save', function () {
    if (pickerSelect){
        $('#calendar-date-time-picker-modal').modal('hide');
    } else {
        $('#calendar-date-time-picker-error-notification').html('Please select a date time!').fadeIn().delay(5000).fadeOut();
    }
    return false;
});

$(document).on('click', '.calendar-date-time-picker-cancel', function () {
    $('#calendar-date-time-picker-modal').modal('hide');
    return false;
});

$.fn.calendarPicker = function(options) {
    pickerSelect = false;
    calendar.refreshCalendar(options);
};

var calendar = {
    refreshCalendar: function (options) {
        var params = $.param({ id: options.teacherId });
        $.ajax({
            url: '/admin/teacher-availability/availability-with-events?' + params,
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                var calendarOptions = { 
                    availableHours: response.availableHours,
                    events: response.events,
                    minTime: response.minTime,
                    maxTime: response.maxTime,
                    duration: options.duration
                };
                calendar.showCalendar(calendarOptions);
            }
        });
    },
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
            events: calendarOptions.events,
            select: function (start) {
                $('#calendar-date-time-picker').fullCalendar('removeEvents', 'newEnrolment');
                pickerSelect = true;
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

