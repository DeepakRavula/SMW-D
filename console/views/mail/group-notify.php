<?php

use common\models\Location;
use yii\helpers\ArrayHelper;
use common\models\Student;
use yii\bootstrap\Html;
use common\models\GroupLesson;
use common\models\CustomerEmailNotification;

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
        $lessonDate = Yii::$app->formatter->asDate($date);
        $lessonTime = (new \DateTime($date))->format('H:i:s');
        $groupLesson;
        $startDate = !empty($lessonDate) ? $lessonDate.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
        if (!empty($enrolmentId)) {
            $groupLesson = GroupLesson::find()->andWhere(['lessonId' => $lessonId])->enrolment($enrolmentId)->all();
        } else {
            $groupLesson = GroupLesson::find()->andWhere(['lessonId' => $lessonId])->all();
        }
            foreach($groupLesson as $lesson) {
                $total = $lesson->total;
                $remainingBalance = $lesson->balance; 
            }
            if($type == CustomerEmailNotification::OVERDUE_INVOICE){
                $amount = Yii::$app->formatter->asCurrency(round($total, 2));
                $balance = Yii::$app->formatter->asBalance(round($remainingBalance ?? 0, 2));
            }
    ?>
    <h3> <?= $emailTemplate->header;?> <br> 
    <?= 'Hello ' . $studentName . ' please check the following ' . $message . ' details.'; ?> </h3>
<table width="100%" cellspacing="0" cellpadding="0" border="1">
    <thead>
    <tr>
        <th>Lesson Date</th>
        <th>Student Name</th>
        <th>Course Name</th>
        <th>Teacher Name</th>
        <?php if($type == CustomerEmailNotification::OVERDUE_INVOICE){ ?>
            <th>Amount</th>
            <th>Balance</th>
        <?php
            }
        ?>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td align="center" valign="top" bgcolor="#ffffff"><?= $startDate; ?> </td> 
            <td align="center" valign="top" bgcolor="#ffffff" > <?= $studentName; ?> </td>
            <td align="center" valign="top" bgcolor="#ffffff"> <?= $courseName; ?> </td>
            <td align="center" valign="top" bgcolor="#ffffff"> <?= $teacherName; ?> </td>
            <?php if($type == CustomerEmailNotification::OVERDUE_INVOICE){ ?>
                <td align="center" valign="top" bgcolor="#ffffff"> <?= $amount; ?> </td>
                <td align="center" valign="top" bgcolor="#ffffff"> <?= $balance; ?> </td>
            <?php
                }
            ?>
        </tr>
    </tbody>
</table>
<?= $emailTemplate->footer ?? null ;;?>
</body>
</html>