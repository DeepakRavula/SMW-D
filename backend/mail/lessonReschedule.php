<?php
use yii\helpers\Html;
use common\models\User;

?>

Dear <?php echo Html::encode($toName) ?>,<br>
<br>
Your <?php echo Html::encode($program) ?> lesson has been rescheduled from <?php echo Html::encode($fromDate) ?> to <?php echo Html::encode($toDate) ?>.<br>
<br>
Thank you<br>
Arcadia Music Academy Team.<br>