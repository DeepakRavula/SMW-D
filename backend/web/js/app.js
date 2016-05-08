$(function() {
    "use strict";

    //Make the dashboard widgets sortable Using jquery UI
    $(".connectedSortable").sortable({
        placeholder: "sort-highlight",
        connectWith: ".connectedSortable",
        handle: ".box-header, .nav-tabs",
        forcePlaceholderSize: true,
        zIndex: 999999
    }).disableSelection();
    $(".connectedSortable .box-header, .connectedSortable .nav-tabs-custom").css("cursor", "move");
});

$(document).ready(function() {
  var date = new Date();
  var d = date.getDate();
  var m = date.getMonth();
  var y = date.getFullYear();

  $('#calendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek,resourceDay'
    },
    defaultView: 'resourceDay',
    editable: true,
    droppable: true,
    resources: [{
      'id': 'resource1',
      'name': 'Resource 1'
    }, {
      'id': 'resource2',
      'name': 'Resource 2'
    }, {
      'id': 'resource3',
      'name': 'Resource 3',
      'className': ['red']
    }, {
      'id': 'resource4',
      'name': 'Resource 4',
    }, {
      'id': 'resource4',
      'name': 'Resource 4',
    }],
    events: [{
      title: 'R1-R2: Lunch 12.15-14.45',
      start: new Date(y, m, d, 12, 15),
      end: new Date(y, m, d, 14, 45),
      allDay: false,
      resources: ['resource1', 'resource2']
    }, {
      title: 'R1: All day',
      start: new Date(y, m, d, 10, 30),
      end: new Date(y, m, d, 11, 0),
      allDay: true,
      resources: 'resource1'
    }, {
      title: 'R2: Meeting 11.00',
      start: new Date(y, m, d, 11, 0),
      allDay: true,
      resources: 'resource2'
    }, {
      title: 'R1/R2: Lunch 12-14',
      start: new Date(y, m, d, 12, 0),
      end: new Date(y, m, d, 14, 0),
      allDay: false,
      resources: ['resource1', 'resource2']
    }, {
      id: 777,
      title: 'Lunch',
      start: new Date(y, m, d, 12, 0),
      end: new Date(y, m, d, 14, 0),
      allDay: false,
      resources: ['resource1']
    }, {
      id: 999,
      title: 'Repeating Event',
      start: new Date(y, m, d - 3, 16, 0),
      allDay: false,
      resources: 'resource2'
    }, {
      id: 999,
      title: 'Repeating Event',
      start: new Date(y, m, d + 4, 16, 0),
      allDay: false,
      resources: 'resource2'
    }],
    // the 'ev' parameter is the mouse event rather than the resource 'event'
    // the ev.data is the resource column clicked upon
    selectable: true,
    selectHelper: true,
    select: function(start, end, ev) {
      console.log(start);
      console.log(end);
      console.log(ev.data); // resources
    },
    eventClick: function(event) {
      console.log(event);
    },
    eventDrop: function (event, delta, revertFunc) {
      console.log(event);
    }
  });
});
