<?php
use common\models\Note;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="lesson-note-content p-10">
<?=
	$this->render('_view', [
		'noteDataProvider' =>  $noteDataProvider,
		'model' => $model,
]);
?>
</div>
