<?php

return [
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        // url rules
        'calendar/<slug:[\w-]+>' => 'calendar/view',
        'calendar/<date:\d{4}-\d{2}-\d{2}>/day-event/<slug:[\w-]+>' => 'calendar/day-event',
        'calendar/<date:\d{4}-\d{2}-\d{2}>/classroom-event/<slug:[\w-]+>' => 'calendar/classroom-event',
        'course/<groupCourseId:\d+>/student/<studentId:\d+>' => 'course/view-student',
        'student/<studentId:\d+>/enrolment/<enrolmentId:\d+>/program-type/<programType:\d+>/delete-preview' => 'student/delete-enrolment-preview',
        'course/<courseId:\d+>/lesson-review' => 'lesson/review',
        'student/<id:\d+>/enrolment' => 'student/enrolment',
    ],
];
