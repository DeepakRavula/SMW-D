<?php

use yii\helpers\Html;
use yii\widgets\ListView;
?>
<div class="row-fluid p-20">
	<div class="col-md-6">
		<div class="row-fluid">
		<p class="m-0"><i class="fa fa-map-marker"></i> Address </p>
		  <?php echo ListView::widget([
		        'dataProvider' => $addressDataProvider,
		        'itemView'=>'_view-address', 
		    ]); ?>
		</div>
	</div>
	<div class="col-md-6">
		<div class="row-fluid m-t-10 m-b-20">
			<div class="col-xs-2 p-0"><i class="fa fa-envelope"></i>  Email</p></div>
			<div class="col-xs-3"><?php echo!empty($model->customer->email) ? $model->customer->email : null ?></div>
			<div class="clearfix"></div>
		</div>
		<div class="row-fluid">
			<p class="m-0"><i class="fa fa-phone-square"></i> Phone number</p>
			<?php echo ListView::widget([
		        'dataProvider' => $phoneDataProvider,
		        'itemView'=>'_view-phone', 
		    ]); ?>
		</div>
	</div>
	<div class="clearfix"></div>
</div>
