<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\models\Qualification;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
 <?php yii\widgets\Pjax::begin(['id' => 'group-grid']); ?>
<?php
	LteBox::begin([
		'type' => LteConst::TYPE_DEFAULT,
		'title' => 'Group Qualifications',
		'boxTools' => '<i class="fa fa-plus add-new-group-qualification"></i>',
		'withBorder' => true,
	])
	?>
			<?php echo GridView::widget([
			 'id' => 'qualification-grid',
				'dataProvider' => $groupQualificationDataProvider,
				'tableOptions' => ['class' => 'table table-condensed'],
				'headerRowOptions' => ['class' => 'bg-light-gray'],
			 	'summary' => '',
				'columns' => [
					'program.name',
					'rate:currency',
			],
			]); ?>	
	<?php LteBox::end() ?>
<?php yii\widgets\Pjax::end(); ?>
<?php
	Modal::begin([
		'header' => '<h4 class="m-0">Edit Qualification</h4>',
		'id'=>'qualification-edit-modal',
	]);?>
	<div id="qualification-edit-content"></div>
	<?php Modal::end();?>		
	<?php
	Modal::begin([
		'header' => '<h4 class="m-0">Add Group Qualification</h4>',
		'id'=>'group-qualification-modal',
	]);
	echo $this->render('/qualification/_form-group', [
		'model' => new Qualification(),
		'userModel' => $model, 
	]);
	 Modal::end();?>	