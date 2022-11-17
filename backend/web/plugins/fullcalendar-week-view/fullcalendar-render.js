$.fn.calendarDayView = function(options) {
    $('#week-view-spinner').show();
    calendar.init(options);
    
    $(document).off('change', '#fullcalendar-week-view-go-to-datepicker').
            on('change', '#fullcalendar-week-view-go-to-datepicker', function () {
        $('#week-view-spinner').show();
        options.date = null;
        calendar.render(options);
    });
    if (options.changeId) {
        $(document).off('change', options.changeId).on('change', options.changeId, function () {
            $('#week-view-spinner').show();
            options.teacherId = null;
            calendar.init(options);
        });
    }
    $(document).off('change', '#week-calendar-show-all').on('change', '#week-calendar-show-all', function () {
        $('#week-view-spinner').show();
        calendar.init(options);
    });
};

var calendar = {
    showCalendar: function (calendarOptions) {
        $('#week-view-calendar').fullCalendar('destroy');
        $('#week-view-calendar').fullCalendar({
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            defaultDate: moment(calendarOptions.date).format('YYYY-MM-DD'),
            firstDay : 1,
            nowIndicator: true,
            header: {
                left: '',
                center: 'title',
                right: ''
            },
            allDaySlot: false,
            contentHeight: calendarOptions.size ? calendarOptions.size : 450,
            slotDuration: '00:15:00',
            titleFormat: 'DD-MMM-YYYY, dddd',
            defaultView: 'agendaWeek',
            minTime: calendarOptions.minTime,
            maxTime: calendarOptions.maxTime,
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
            businessHours: calendarOptions.businessHours,
            allowCalEventOverlap: true,
            overlapEventsSeparate: true,
            events: {
                url: calendarOptions.eventUrlFormatted,
                type: 'GET',
                error: function() {
                    //$('#week-view-calendar').fullCalendar("refetchEvents");
                }
            },
            select: function (start) {
                $('#week-view-calendar').fullCalendar('removeEvents', 'newEnrolment');
                var response = {
                    'date': moment(start).format('DD-MM-YYYY h:mm A')
                };
                $(document).trigger("week-view-calendar-select", response);
                var endtime = start.clone();
                var duration = calendarOptions.duration ? calendarOptions.duration : $(calendarOptions.durationId).val();
                var durationMinutes = moment.duration($.isEmptyObject(duration) ? '00:30' : duration).asMinutes();
                moment(endtime.add(durationMinutes, 'minutes'));
                $('#week-view-calendar').fullCalendar('renderEvent',
                    {
                        id: 'newEnrolment',
                        start: start,
                        end: endtime,
                        allDay: false
                    },
                true // make the event "stick"
                );
                $('#week-view-calendar').fullCalendar('unselect');
            },
            eventAfterRender: function () {
                //$(document).trigger("week-calendar-after-render");
            },
            eventAfterAllRender: function () {
                $('.fc-short').removeClass('fc-short');
                $('#week-view-spinner').hide();
            },
            selectable: true,
            selectHelper: true
        });
    },

    render: function (options) {
        if (options.date) {
            $('#fullcalendar-week-view-go-to-datepicker').val(moment(options.date).format('MMM D, YYYY'));
            options.date = null;
        }
        var teacherId = options.teacherId ? options.teacherId : $(options.changeId).val();
        var now = Date();
        var dateValue = $('#fullcalendar-week-view-go-to-datepicker').val();
        var date = moment(dateValue ? dateValue : now).format('MMM D, YYYY');
        var eventParams = $.param({ 
            'LessonSearch[teacherId]': teacherId, 
            'LessonSearch[date]': date, 
            'LessonSearch[studentId]': options.studentId, 
            'LessonSearch[lessonId]': options.lessonId,
            'LessonSearch[enrolmentId]': options.enrolmentId 
        });
        var calendarOptions = options;
        calendarOptions.date = date;
        calendarOptions.eventUrlFormatted = options.eventUrl + '?' + eventParams;
        calendar.showCalendar(calendarOptions);
    },
    
    init: function(options) {
        var teacherId = options.teacherId ? options.teacherId : $(options.changeId).val();
        var params = $.param({ id: teacherId , 'ScheduleSearch[locationVisibility]': $('#week-calendar-show-all').is(":checked") | 0 });
        var url = teacherId ? options.availabilityUrl + '?' + params : options.availabilityUrl;
        $.ajax({
            url: url,
            type: 'get',
            success: function (response)
            {
                $(options.renderId).html(response.data);
                var calendarOptions = options;
                calendarOptions.businessHours = response.availableHours;
                calendarOptions.minTime = response.minTime;
                calendarOptions.maxTime = response.maxTime;
                calendar.render(calendarOptions);
            }
        });
    }
};

