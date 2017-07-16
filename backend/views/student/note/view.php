<?php

use common\models\Note;
use yii\widgets\ListView;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php yii\widgets\Pjax::begin([
    'id' => 'student-note',
    'timeout' => 6000,
]) ?>
<div class="row p-10">
<div id="student-note" class="col-md-12">
	<h4 class="pull-left m-r-20">Notes</h4>
	<a href="#" class="text-add-new"><i class="fa fa-plus"></i></a>
	<div class="clearfix"></div>
</div>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Notes</h4>',
    'id'=>'student-note-modal',
]);
 echo $this->render('_form', [
		'model' => new Note(),
        'studentModel' => $studentModel,
]);
Modal::end();
?>
<div class="student-note-content">
<?=
	$this->render('_view', [
		'noteDataProvider' =>  $noteDataProvider,
		'itemView' => '_list',
]);
?>
</div>
	
	
</div>
<?php \yii\widgets\Pjax::end(); ?>
