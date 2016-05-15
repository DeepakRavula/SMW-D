<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Json;

/* @var $this yii\web\View */

$this->title = 'Schedule';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schedule-index">
<div id='calendar'></div>
</div>
<script type="text/javascript">
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
    resources:  <?php echo Json::encode($teacherAvailability); ?>,
    events: [{
      title: '[AM] Karen Johnson (Guitar)',
      start: new Date(y, m, d, 10, 15),
      end: new Date(y, m, d, 11, 45),
      allDay: false,
      resources: ['1', '2']
    }, {
      title: '[AM] Jake Attal (Piano)',
      start: new Date(y, m, d, 11, 0),
      end: new Date(y, m, d, 12, 30),
      allDay: false,
      resources: '150'
    }, {
      title: '[PM] Karen Johnson (Violin)',
      start: new Date(y, m, d, 13, 0),
	   end: new Date(y, m, d, 14, 30),
      allDay: false,
      resources: '152'
    }, {
      title: '[PM] Jake Attal (Guitar)',
      start: new Date(y, m, d, 15, 0),
      end: new Date(y, m, d, 16, 30),
      allDay: false,
      resources: ['153', '154']
    }, {
      id: 777,
      title: '[PM] Tiffany Kong (Piano)',
      start: new Date(y, m, d, 17, 0),
      end: new Date(y, m, d, 18, 30),
      allDay: false,
      resources: ['155']
    }, {
      id: 999,
      title: '[PM] Kimberly Aquino (Guitar)',
      start: new Date(y, m, d, 19, 0),
      end: new Date(y, m, d, 20, 30),
      allDay: false,
      resources: '155'
    }, {
      
       title: 'Nicholas Hanns (Guitar)',
      start: new Date(y, m, d, 21, 0),
      end: new Date(y, m, d, 22, 30),
      allDay: false,
      resources: '153'
    },{
      
      title: 'Rita Fabian (Piano)',
      start: new Date(y, m, d, 23, 0),
      end: new Date(y, m, d, 00, 0),
      allDay: false,
      resources: '155'
    }, {
      
       title: '[AM] Vanessa Pryia (Guitar)',
      start: new Date(y, m, d, 3, 0),
      end: new Date(y, m, d, 4, 30),
      allDay: false,
      resources: '155'
    },{
      
       title: '[AM] Paul Faggil (Piano)',
      start: new Date(y, m, d, 4, 0),
      end: new Date(y, m, d, 5, 30),
      allDay: false,
      resources: '151'
    },{
     
       title: '[AM] Giselle Andrews (Guitar)',
      start: new Date(y, m, d, 6, 0),
      end: new Date(y, m, d, 7, 30),
      allDay: false,
      resources: '152'
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
</script>