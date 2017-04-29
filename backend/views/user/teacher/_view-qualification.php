<?php

use yii\helpers\Html;
use yii\grid\GridView;
?>
<div class="row-fluid m-t-20 p-10">
	<div>
		<h5 class="m-t-5"><i class="fa fa-graduation-cap"></i> Qualifications</h5>
	</div>
	<div class="row p-10">
	<div class="col-xs-6">
		<div class="row-fluid">
		<p class="c-title m-0 p-10"><i class="fa fa-music"></i> Private Programs </p>
		 <?php echo GridView::widget([
					'dataProvider' => $privateQualificationDataProvider,
					'tableOptions' => ['class' => 'table table-bordered'],
					'headerRowOptions' => ['class' => 'bg-light-gray'],
					'columns' => [
						'program.name',
						'rate:currency',
				],
			]); ?>	
		</div>
	</div>
	<div class="col-xs-6">
		<div class="row-fluid">
			<p class="c-title m-0 p-10"><i class="fa fa-music"></i> Group Programs</p>
			<?php echo GridView::widget([
					'dataProvider' => $groupQualificationDataProvider,
					'tableOptions' => ['class' => 'table table-bordered'],
					'headerRowOptions' => ['class' => 'bg-light-gray'],
					'columns' => [
						'program.name',
						'rate:currency',
				],
			]); ?>	
		</div>
		
	</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-md-12 m-t-20 m-b-20">
		<?php echo Html::a('<i class="fa fa-pencil"></i> Edit Qualification', ['update', 'UserSearch[role_name]' => $searchModel->role_name, 'id' => $model->id, '#' => 'qualification'], ['class' => 'm-r-20']) ?>
	</div>
</div>
