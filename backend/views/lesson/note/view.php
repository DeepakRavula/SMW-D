<?php

use common\models\Note;
use yii\widgets\ListView;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div id="lesson-note" class="col-md-12">
	<h4 class="pull-left m-r-20">Notes</h4>
	<a href="#" class="text-add-new"><i class="fa fa-plus"></i></a>
	<div class="clearfix"></div>
</div>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Notes</h4>',
    'id'=>'lesson-note-modal',
]);
 echo $this->render('_form', [
		'model' => new Note(),
]);
Modal::end();
?>
<div>
<?php yii\widgets\Pjax::begin([
	'id' => 'lesson-note-listing',
	'timeout' => 6000,
]) ?>
<?php echo ListView::widget([
	'dataProvider' =>  $noteDataProvider,
	'itemView' => '_view',
]); ?>
</div>
<?php \yii\widgets\Pjax::end(); ?>
