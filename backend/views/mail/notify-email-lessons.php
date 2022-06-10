
<?php 
use yii\bootstrap\Html;
 ?>
<?= Html::label('Lessons', ['class' => 'admin-login']) ?>
    <?= $this->render('print-notify-email-lessons', [
        'modelPf' => $modelPf,
        'isCreatePfi' => false,
        'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
        'searchModel' => $searchModel
    ]);
    ?>