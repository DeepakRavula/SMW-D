\<?php

return [
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        // url rules
        'location-view' => 'location/view',
        'location-update' => 'location/update',
        'calendar/render-classroom-resources' => 'calendar/render-classroom-resources',
        'calendar/render-classroom-events' => 'calendar/render-classroom-events',
        'calendar/render-day-events' => 'calendar/render-day-events',
        'calendar/render-resources' => 'calendar/render-resources',
        'calendar/<slug:[\w-]+>' => 'calendar/view',
        'calendar/<date:\d{4}-\d{2}-\d{2}>/day-event/<slug:[\w-]+>' => 'calendar/day-event',
        'calendar/<date:\d{4}-\d{2}-\d{2}>/classroom-event/<slug:[\w-]+>' => 'calendar/classroom-event',
        'student/<studentId:\d+>/enrolment/<enrolmentId:\d+>/program-type/<programType:\d+>/delete-preview' => 'student/delete-enrolment-preview',
        'course/<courseId:\d+>/lesson-review' => 'lesson/review',
        'student/<id:\d+>/create-enrolment' => 'student/create-enrolment',
        'course/<courseId:\d+>/enrolment/<enrolmentId:\d+>/lesson-review' => 'lesson/group-enrolment-review',
    ],
];
