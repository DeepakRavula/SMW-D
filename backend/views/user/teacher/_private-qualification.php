<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\models\Qualification;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
<?php yii\widgets\Pjax::begin(['id' => 'private-grid']); ?>
<?php
	LteBox::begin([
		'type' => LteConst::TYPE_DEFAULT,
		'title' => 'Private Qualifications',
		'boxTools' => '<i class="fa fa-plus add-new-qualification"></i>', 
		'withBorder' => true,
	])
	?>
		 <?php echo GridView::widget([
			 'id' => 'qualification-grid',
			'dataProvider' => $privateQualificationDataProvider,
			 'summary' => '',
			'tableOptions' => ['class' => 'table table-condensed'],
			'headerRowOptions' => ['class' => 'bg-light-gray'],
			'columns' => [
				'program.name',
				[
					'label' => 'Rate ($/hr)',
					'format' => 'currency',
					'value' => function($data) {
			 			return $data->rate;
					},
					'visible' => Yii::$app->user->can('viewQualificationRate') 
				]
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
		'header' => '<h4 class="m-0">Add Private Qualification</h4>',
		'id'=>'private-qualification-modal',
	]);
	echo $this->render('/qualification/_form-private', [
		'model' => new Qualification(),
		'userModel' => $model, 
	]);
	Modal::end();?>	