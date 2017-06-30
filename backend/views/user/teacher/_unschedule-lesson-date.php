<?php

?>
<?php if(!empty($model->date)) : ?>
<?= Yii::$app->formatter->asDate($model->date);?> 
<span id="unschedule-calendar"><i class="fa fa-calendar"></i></span>
<?php endif; ?>
