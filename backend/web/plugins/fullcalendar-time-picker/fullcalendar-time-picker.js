$.fn.calendarPicker = function(options) {
    $(options.parentPopUp).modal('hide');
    $('#calendar-date-time-picker-modal').modal('show');
    $('#calendar-date-time-picker-modal .modal-dialog').css({'width': '1000px'});
    
    $(document).off('shown.bs.modal', '#calendar-date-time-picker-modal').on('shown.bs.modal', 
        '#calendar-date-time-picker-modal', function () {
        if (options.teacherData) {
            $("#calendar-date-time-picker-teacher").empty();
            $("#calendar-date-time-picker-teacher").off().select2({
                data: options.teacherData,
                width: '100%',
                theme: 'krajee'
            });
        }
        $('#calendar-date-time-picker-teacher').val(options.teacherId);
        pickerCalendar.renderCalendar(options);
    });
    
    $(document).off('click', '.calendar-date-time-picker-cancel').on('click', 
        '.calendar-date-time-picker-cancel', function () {
        $('#calendar-date-time-picker-modal').modal('hide');
        return false;
    });

    $(document).off('hidden.bs.modal', '#calendar-date-time-picker-modal').on('hidden.bs.modal', 
        '#calendar-date-time-picker-modal', function () {
        $(document).trigger( "after-picker-close");
    });
    
    $(document).off('click', '.calendar-date-time-picker-cancel').on('click', 
        '.calendar-date-time-picker-cancel', function () {
        $('#calendar-date-time-picker-modal').modal('hide');
        return false;
    });

    $(document).off('hidden.bs.modal', '#calendar-date-time-picker-modal').on('hidden.bs.modal', 
        '#calendar-date-time-picker-modal', function () {
        $(document).trigger( "after-picker-close");
    });
    
    $(document).off('click', '.calendar-date-time-picker-save').on('click', '.calendar-date-time-picker-save', function () {
        if (!$.isEmptyObject($('#week-view-calendar').fullCalendar('clientEvents', 'newEnrolment'))) {
            $('#calendar-date-time-picker-modal').modal('hide');
            var selecetdEvent = $('#week-view-calendar').fullCalendar('clientEvents', 'newEnrolment');
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
    
    var pickerCalendar = {
        renderCalendar: function (options) {
            var calendarOptions = options;
            calendarOptions.renderId = '#calendar-date-time-picker';
            $.fn.calendarDayView(calendarOptions);
        }
    };
};

