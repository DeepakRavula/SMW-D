<?php

use common\models\Location;
use yii\helpers\ArrayHelper;
use common\models\Student;
use yii\bootstrap\Html;
use common\models\GroupLesson;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<title>SMW Arcadia Musical School</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0 " />
<meta name="format-detection" content="telephone=no"/>  
</head>
<body>
<?php
     foreach($contents->query->all() as $data) { 
        if($data->course->isPrivate()){
            $date = Yii::$app->formatter->asDate($data->date);
            $lessonTime = (new \DateTime($data->date))->format('H:i:s');
            $startDate = !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
            $studentName = $data->course->enrolment->student->fullName ?? null;
            $courseName = $data->course->program->name ?? null;
            $teacherName = $data->teacher->publicIdentity ?? null;
            $amount = Yii::$app->formatter->asCurrency(round($data->privateLesson->total ?? 0, 2));
            $balance = Yii::$app->formatter->asBalance(round($data->privateLesson->balance ?? 0, 2));
        } else {
            $groupLesson = GroupLesson::find()->andWhere(['lessonId' => $data->id])->all();
            foreach($groupLesson as $lesson) {
                $total = $lesson->total;
                $remainingBalance = $lesson->balance; 
            }

            $date = Yii::$app->formatter->asDate($data->date);
            $lessonTime = (new \DateTime($data->date))->format('H:i:s');
            $startDate = !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
            $studentName = $data->enrolment->student->fullName ?? null;
            $courseName = $data->course->program->name ?? null;
            $teacherName = $data->teacher->publicIdentity ?? null;
            $amount = Yii::$app->formatter->asCurrency(round($total, 2));
            $balance = Yii::$app->formatter->asBalance(round($remainingBalance ?? 0, 2));
        } 
    ?>
    <h3> <?= 'Hello ' . $studentName . ' Please check the following ' . $message . ' details.'; ?> </h3>
<table width="100%" cellspacing="0" cellpadding="0" border="1">
    <thead>
    <tr>
        <th>Start Date</th>
        <th>Student Name</th>
        <th>Course Name</th>
        <th>Teacher Name</th>
        <th>Amount</th>
        <th>Balance</th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td align="center" valign="top" bgcolor="#ffffff"><?= $startDate; ?> </td> 
            <td align="center" valign="top" bgcolor="#ffffff" > <?= $studentName; ?> </td>
            <td align="center" valign="top" bgcolor="#ffffff"> <?= $courseName; ?> </td>
            <td align="center" valign="top" bgcolor="#ffffff"> <?= $teacherName; ?> </td>
            <td align="center" valign="top" bgcolor="#ffffff"> <?= $amount; ?> </td>
            <td align="center" valign="top" bgcolor="#ffffff"> <?= $balance; ?> </td>
            
        </tr>
    </tbody>
</table>
<?php    
    } 
    ?>    
</body>
</html>