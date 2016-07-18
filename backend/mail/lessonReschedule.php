<?php
use yii\helpers\Html;
use common\models\User;

?>

Dear <?php echo Html::encode($to_name) ?>,<br>
<br>
Your <?php echo Html::encode($program) ?> lesson has been rescheduled from <?php echo Html::encode($from_date) ?> to <?php echo Html::encode($to_date) ?>.<br>
<br>
Thank you<br>
Arcadia Music Academy Team.<br>