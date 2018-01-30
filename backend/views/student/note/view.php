<?php

?>
<?php yii\widgets\Pjax::begin([
    'id' => 'student-note',
    'timeout' => 6000,
]) ?>
<div class="student-note-content">
<?=
    $this->render('_view', [
        'noteDataProvider' =>  $noteDataProvider,
        'model' => $model,
]);
?>
</div>
<?php \yii\widgets\Pjax::end(); ?>
