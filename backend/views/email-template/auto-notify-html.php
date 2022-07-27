<?php

use common\models\Location;
use yii\helpers\ArrayHelper;
use common\models\Student;
use yii\bootstrap\Html;

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
<style type="text/css">
body { margin: 0 auto; padding: 0; -webkit-text-size-adjust: 100% !important; -ms-text-size-adjust: 100% !important; -webkit-font-smoothing: antialiased !important; }
img { border: 0 !important; outline: none !important; }
p { Margin: 0px !important; Padding: 0px !important; }
table { border-collapse: collapse; mso-table-lspace: 0px; mso-table-rspace: 0px; }
td, a, span { border-collapse: collapse; mso-line-height-rule: exactly; }
.ExternalClass * { line-height: 100%; }
.em_defaultlink a { color: inherit; text-decoration: none; }
.em_defaultlink2 a { color: inherit; text-decoration: underline; }
.em_g_img + div { display: none; }
a[x-apple-data-detectors], u + .em_body a, #MessageViewBody a { color: inherit; text-decoration: none; font-size: inherit; font-family: inherit; font-weight: inherit; line-height: inherit; }
 @media only screen and (max-width:599px) {
.em_hide { display: none !important; }
}
</style>  
</head>
<body>
<table>
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
        
    <?php
     foreach($contents->query->all() as $data) { ?>
        <tr>
    
            <td> <?=$data->date ?> </td> 
            <td> <?= $data->enrolment->student->fullName ?> </td>
            <td> <?= $data->program->name ?> </td>
            <td> <?= $data->teacherProfile->firstname .' '. $data->teacherProfile->lastname ?> </td>
            <td> <?= Yii::$app->formatter->asCurrency(round($data->privateLesson->total ?? 0, 2)) ?> </td>
            <td> <?= $data->privateLesson->balance ?> </td>
            
        </tr>
        <?php 
        
        ?>
       <?php    
        die;} 
        ?>    
    </tbody>
</table>
</body>
</html>