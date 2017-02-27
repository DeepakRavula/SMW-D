<?php
use yii\helpers\Html;
?>

Dear <?php echo Html::encode($toName) ?>,<br> 
  <?= $content; ?>
<br>
<table cellspacing="0" cellpadding="3" border="1" style="width:100%">
        <tr>
            <td>Date</td>
            <td><?php echo Html::encode(Yii::$app->formatter->asDateTime($model->date)); ?></td>
        </tr>
        <tr>
            <td>Teacher</td>
            <td><?= $model->teacher->publicIdentity; ?></td>
        </tr>
        <tr>
            <td>Student</td>
            <td><?= !empty($model->enrolment->student->fullName) ? $model->enrolment->student->fullName : null; ?></td>
        </tr>
        <tr>
            <td>Duration</td>
            <td><?= (new \DateTime($model->duration))->format('H:i'); ?></td>
        </tr>
	</table>
<br>
Thank you<br>
Arcadia Music Academy Team.<br>