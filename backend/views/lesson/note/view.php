<?php

use common\models\Note;
use yii\widgets\ListView;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
	.lesson-note-content .empty{
		padding:0;
	}
</style>
<div class="lesson-note-content p-10">
<?php echo ListView::widget([
	'dataProvider' =>  $noteDataProvider,
	'itemView' => '_list',
]); ?>
	<!-- /.chat -->
<div class="box-footer">
	<div class="input-group">
		<input class="form-control" placeholder="Type message...">

		<div class="input-group-btn">
			<button type="button" class="btn btn-success"><i class="fa fa-plus"></i></button>
		</div>
	</div>
</div>
</div>
