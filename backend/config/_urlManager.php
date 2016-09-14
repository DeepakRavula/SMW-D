<?php
return [
    'class'=>'yii\web\UrlManager',
    'enablePrettyUrl'=>true,
    'showScriptName'=>false,
    'rules'=>[
        // url rules
		'calendar/<slug:[\w-]+>' => 'calendar/view',
        'course/<groupCourseId:\d+>/student/<studentId:\d+>' => 'course/view-student',
        'student/<studentId:\d+>/enrolment/<enrolmentId:\d+>/program-type/<programType:\d+>/delete-preview' => 'student/delete-enrolment-preview',
        'student/<id:\d+>/enrolment/<courseId:\d+>/lesson-review' => 'student/lesson-review',
        'student/<id:\d+>/enrolment' => 'student/enrolment',
        'student/<id:\d+>/enrolment/<courseId:\d+>/lesson-confirm' => 'student/lesson-confirm',
    ]
];
