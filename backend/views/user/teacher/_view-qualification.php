<?php

use yii\helpers\Html;

?>
<div class="row-fluid m-t-20 p-10">
	<div>
		<h5 class="m-t-5"><i class="fa fa-graduation-cap"></i> Qualifications</h5>
	</div>
	<div class="col-xs-4">
		<div class="row-fluid m-t-10 m-b-20">
			<div class="col-xs-6 p-0 c-title"> Private Programs </div>
			<div class="col-xs-4">
				<span class="label label-primary"><?= $program ?></span>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row-fluid m-t-10 m-b-20">
			<div class="col-xs-6 p-0 c-title"> Group Programs </div>
			<div class="col-xs-4">
				<span class="label label-primary"><?= $groupPrograms ?></span>
			</div>
			<div class="clearfix"></div>
		</div>	
			
	</div>
	<div class="clearfix"></div>
	<div class="col-md-12 m-t-20 m-b-20">
		<?php echo Html::a('<i class="fa fa-pencil"></i> Edit Qualification', ['update', 'UserSearch[role_name]' => $searchModel->role_name, 'id' => $model->id, '#' => 'qualification'], ['class' => 'm-r-20']) ?>
	</div>
</div>
