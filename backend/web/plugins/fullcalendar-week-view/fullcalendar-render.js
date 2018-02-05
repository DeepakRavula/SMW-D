$.fn.calendarDayView = function(options) {
    calendar.render(options);
    
    $(document).off('change', options.dateId).on('change', options.dateId, function () {
        calendar.render(options);
    });
    
    $(document).off('change', options.changeId).on('change', options.changeId, function () {
        calendar.render(options);
    });
};

var calendar = {
    showCalendar: function (calendarOptions) {
        $(calendarOptions.renderId).fullCalendar('destroy');
        $(calendarOptions.renderId).fullCalendar({
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            defaultDate: moment(calendarOptions.date).format('YYYY-MM-DD'),
            firstDay : 1,
            nowIndicator: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            allDaySlot: false,
            height: 500,
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
                url: calendarOptions.eventUrl,
                type: 'GET',
                error: function() {
                    $(calendarOptions.renderId).fullCalendar("refetchEvents");
                }
            },
            select: function (start) {
                $(calendarOptions.renderId).fullCalendar('removeEvents', 'newEnrolment');
                $(calendarOptions.dateRenderId).val(moment(start).format('DD-MM-YYYY h:mm A')).trigger('change');
                var endtime = start.clone();
                var duration = $(calendarOptions.durationId).val();
                var durationMinutes = moment.duration($.isEmptyObject(duration) ? '00:30' : duration).asMinutes();
                moment(endtime.add(durationMinutes, 'minutes'));
                $(calendarOptions.renderId).fullCalendar('renderEvent',
                    {
                        id: 'newEnrolment',
                        start: start,
                        end: endtime,
                        allDay: false
                    },
                true // make the event "stick"
                );
                $(calendarOptions.renderId).fullCalendar('unselect');
            },
            eventAfterAllRender: function () {
                $('.fc-short').removeClass('fc-short');
            },
            selectable: true,
            selectHelper: true
        });
    },

    render: function (options) {
        var date = moment($(options.dateId).val(), "DD-MM-YYYY");
        var teacherId = $(options.changeId).val();
        var params = $.param({ id: teacherId });
        var eventParams = $.param({ teacherId: teacherId });
        $.ajax({
            url: options.availabilityUrl + '?' + params,
            type: 'get',
            success: function (response)
            {
                var businessHours = response.availableHours;
                var calendarOptions = options;
                calendarOptions.businessHours = businessHours;
                calendarOptions.date = date;
                calendarOptions.eventUrl = options.eventUrl + '?' + eventParams;
                calendar.showCalendar(calendarOptions);
            }
        });
    }
};

