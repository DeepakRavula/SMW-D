$(document).off('click', '.calendar-date-time-picker-cancel').on('click', '.calendar-date-time-picker-cancel', function () {
    $('#calendar-date-time-picker-modal').modal('hide');
    return false;
});

$(document).off('hidden.bs.modal', '#calendar-date-time-picker-modal').on('hidden.bs.modal', '#calendar-date-time-picker-modal', function () {
    $(document).trigger( "after-picker-close");
});

$.fn.calendarPicker = function(options) {
    $('#calendar-date-time-picker-modal').modal('show');
    $('#calendar-date-time-picker-modal .modal-dialog').css({'width': '1000px'});
    
    $(document).off('shown.bs.modal', '#calendar-date-time-picker-modal').on('shown.bs.modal', '#calendar-date-time-picker-modal', function () {
        if (!$.isEmptyObject(options.teacherData)) {
            $("#calendar-date-time-picker-teacher").empty();
            $("#calendar-date-time-picker-teacher").off().select2({
                data: options.teacherData,
                width: '100%',
                theme: 'krajee'
            });
            
        }
        $('#calendar-date-time-picker-teacher').val(options.teacherId);
        pickerCalendar.showCalendar(options);
    });
    
    $(document).off('change', '#calendar-date-time-picker-date').on('change', '#calendar-date-time-picker-date', function () {
        var validationUrl = options.validationUrl;
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
    
    $(document).off('click', '.calendar-date-time-picker-save').on('click', '.calendar-date-time-picker-save', function () {
        if (!$.isEmptyObject($('#calendar-date-time-picker').fullCalendar('clientEvents', 'newEnrolment'))){
            $('#calendar-date-time-picker-modal').modal('hide');
            var selecetdEvent = $('#calendar-date-time-picker').fullCalendar('clientEvents', 'newEnrolment');
            var params = {
                name: options.name,
                lessonId: options.lessonId,
                date: selecetdEvent[0].start
            };
            $(document).trigger( "after-date-set", params);
        } else {
            $('#calendar-date-time-picker-error-notification').html('Please select a date time!').fadeIn().delay(5000).fadeOut();
        }
        return false;
    });
    
    $(document).off('change', '#calendar-date-time-picker-teacher').on('change', '#calendar-date-time-picker-teacher', function(){
        var date = moment($('#go-to-date').val(), 'DD-MM-YYYY', true).format('YYYY-MM-DD');
        if (! moment(date).isValid()) {
            var date = moment($('#go-to-date').val(), 'YYYY-MM-DD hh:mm A', true).format('YYYY-MM-DD');
        }
        pickerCalendar.teacherChange(options, date);
    });

    $(document).off('change', '#go-to-date').on('change', '#go-to-date', function(){
        var date = moment($('#go-to-date').val(), 'DD-MM-YYYY', true).format('YYYY-MM-DD');
        if (! moment(date).isValid()) {
            var date = moment($('#go-to-date').val(), 'YYYY-MM-DD hh:mm A', true).format('YYYY-MM-DD');
        }
        $('#calendar-date-time-picker').fullCalendar('gotoDate', date);
    });
};
    
var pickerCalendar = {
    showCalendar: function (calendarOptions) {
        if ($.isEmptyObject(calendarOptions.selectConstraint)) {
            calendarOptions.selectConstraint = 'businessHours';
        }
        if ($.isEmptyObject(calendarOptions.eventConstraint)) {
            calendarOptions.eventConstraint = 'businessHours';
        }
        $('#calendar-date-time-picker').fullCalendar('destroy');
        $('#calendar-date-time-picker').fullCalendar({
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            defaultDate: moment(calendarOptions.date).format('YYYY-MM-DD'),
			firstDay : 1,
            nowIndicator: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: '',
            },
            allDaySlot: false,
            height: 500,
            slotDuration: '00:15:00',
            titleFormat: 'DD-MMM-YYYY, dddd',
            defaultView: 'agendaWeek',
            minTime: calendarOptions.minTime,
            maxTime: calendarOptions.maxTime,
            selectConstraint: calendarOptions.selectConstraint,
            eventConstraint: calendarOptions.eventConstraint,
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
    },
    
    teacherChange: function (options, date) {
        var teacherId = $('#calendar-date-time-picker-teacher').val();
        if (teacherId != options.teacherId) {
            var params = $.param({ id: teacherId });
            $.ajax({
                url: '/admin/teacher-availability/availability-with-events?' + params,
                type: 'get',
                success: function (response)
                {
                    var businessHours = response.availableHours;
                    var calendarOptions = {
                        duration: options.duration,
                        selectConstraint: options.selectConstraint,
                        eventConstraint: options.eventConstraint,
                        teacherId: teacherId,
                        date: date,
                        businessHours: businessHours,
                        minTime: options.minTime,
                        maxTime: options.maxTime,
                        eventUrl: '/admin/teacher-availability/show-lesson-event?teacherId=' + teacherId,
                        validationUrl: options.validationUrl
                    };
                    pickerCalendar.showCalendar(calendarOptions);
                }
            });
        }
    }
}

