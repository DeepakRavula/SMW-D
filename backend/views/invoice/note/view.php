<?php

use common\models\Note;
use yii\widgets\ListView;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Notes</h4>
	<a href="#" id="invoice-note" class="text-add-new"><i class="fa fa-plus"></i></a>
	<div class="clearfix"></div>
</div>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Notes</h4>',
    'id'=>'invoice-note-modal',
]);
 echo $this->render('_form', [
		'model' => new Note(),
]);
Modal::end();
?>
<div class="invoice-note-content">
<?=
	$this->render('_view', [
		'noteDataProvider' =>  $noteDataProvider,
		'itemView' => '_list',
]);
?>
</div>
