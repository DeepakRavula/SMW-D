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
    <?php foreach($contents as $data){
    ?>
        <tr>
            <td><?=   
            $date = Yii::$app->formatter->asDate($data->dueDate);
            $lessonTime = (new \DateTime($data->dueDate))->format('H:i:s');
            return !empty($date) ? $date : null;
         ?></td>
            <td>
           <?= ArrayHelper::map(Student::find()
                ->notDeleted()
                ->orderBy(['first_name' => SORT_ASC])
                ->joinWith(['enrolments' => function ($query) {
                    $query->joinWith(['course' => function ($query) {
                        $query->confirmed()
                            ->notDeleted()
                            ->location(Location::findOne(['slug' => \Yii::$app->location])->id);
                    }]);
                }])
                ->customer($model->userId)
                ->all(), 'id', 'fullName') ?>
        </td>
            <td><?= return $model->course->program->name; ?></td>
            <td><?=  $data->teacher->publicIdentity; ?></td>
            <td><?=  return Yii::$app->formatter->asCurrency(round($data->privateLesson->total ?? 0, 2)); ?> </td>
            <td><?=  return Yii::$app->formatter->asBalance(round($data->privateLesson->balance ?? 0, 2)); ?></td>
        </tr>
        <?php  } ?>
    </tbody>
</table>
</body>
</html>