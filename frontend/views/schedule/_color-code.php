<?php

use common\models\CalendarEventColor;

?>
<?php

$teacherAvailability = CalendarEventColor::findOne(['cssClass' => 'teacher-availability']);
$teacherUnavailability = CalendarEventColor::findOne(['cssClass' => 'teacher-unavailability']);
$privateLesson = CalendarEventColor::findOne(['cssClass' => 'private-lesson']);
$groupLesson = CalendarEventColor::findOne(['cssClass' => 'group-lesson']);
$firstLesson = CalendarEventColor::findOne(['cssClass' => 'first-lesson']);
$teacherSubstitutedLesson = CalendarEventColor::findOne(['cssClass' => 'teacher-substituted']);
$rescheduledLesson = CalendarEventColor::findOne(['cssClass' => 'lesson-rescheduled']);
$missedLesson = CalendarEventColor::findOne(['cssClass' => 'lesson-missed']);
$absentLesson = CalendarEventColor::findOne(['cssClass' => 'absent-lesson']);
$this->registerCss(
    ".fc-bgevent { background-color: " . $teacherAvailability->code . " !important; }
        .fc-bg { background-color: " . $teacherUnavailability->code . " !important; }
        .fc-today { background-color: " . $teacherUnavailability->code . " !important; }
        .private-lesson, .fc-event .private-lesson .fc-event-time, .private-lesson a {
            border: 1px solid " . $privateLesson->code . " !important;
            background-color: " . $privateLesson->code . " !important; }
        .first-lesson, .fc-event .first-lesson .fc-event-time, .first-lesson a {
            border: 1px solid " . $firstLesson->code . " !important;
            background-color: " . $firstLesson->code . " !important; }
        .group-lesson, .fc-event .group-lesson .fc-event-time, .group-lesson a {
            border: 1px solid " . $groupLesson->code . " !important;
            background-color: " . $groupLesson->code . " !important; }
        .teacher-substituted, .fc-event .teacher-substituted .fc-event-time, .teacher-substituted a {
            border: 1px solid " . $teacherSubstitutedLesson->code . " !important;
            background-color: " . $teacherSubstitutedLesson->code . " !important; }
        .lesson-rescheduled, .fc-event .lesson-rescheduled .fc-event-time, .lesson-rescheduled a {
            border: 1px solid " . $rescheduledLesson->code . " !important;
            background-color: " . $rescheduledLesson->code . " !important; }
        .absent-lesson, .fc-event .absent-lesson .fc-event-time, .absent-lesson a {
            border: 1px solid " . $absentLesson->code . " !important;
            background-color: " . $absentLesson->code . " !important; }"
);
?>