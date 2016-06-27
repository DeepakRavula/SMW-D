<?php

use yii\helpers\Html;
?>
<div class="row-fluid m-t-20">
	<div class="col-md-2">
		<h5 class="m-t-5"><i class="fa fa-graduation-cap"></i> Qualifications</h5>
	</div>
	<div class="col-md-10">
		<span class="label label-primary"><?= $program ?></span>
	</div>
	<div class="clearfix"></div>
	<div class="col-md-12 m-t-20 m-b-20">
		<?php echo Html::a('<i class="fa fa-pencil"></i> Update Qualification', ['update', 'id' => $model->id,'section' => 'qualification'], ['class' => 'm-r-20']) ?>
	</div>
</div>
