<?php
use yii\helpers\Html;
use common\models\EmailTemplate;
use common\models\EmailObject;
?>

Dear Customer,<br> 
<?= $emailTemplate->header ?>
  <?= $content; ?>
<br>
<?= $emailTemplate->footer ?? 'Thank you<br>
Arcadia Academy of Music Team.' ?><br>