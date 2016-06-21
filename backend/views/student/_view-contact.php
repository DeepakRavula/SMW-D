<?php

use yii\helpers\Html;
use yii\widgets\ListView;
?>
<div class="row-fluid p-b-20">
	<!-- <div class="col-md-6"> -->
	<div class="row-fluid m-t-10">
			<p class="m-0">Email</p>
			<i class="fa fa-envelope"></i> <?php echo!empty($model->customer->email) ? $model->customer->email : null ?>
	</div>
		<div class="row-fluid">
			<i class="fa fa-map-marker"></i>
			<p>Address </p>
  <?php echo ListView::widget([
        'dataProvider' => $addressDataProvider,
        'itemView'=>'_view-address', 
    ]); ?>

		</div>
	
	<!-- </div> -->
<!--	<div class="col-md-6"> -->
		<br>
		<div class="row-fluid">
			<p class="m-0">Phone number</p>
			<i class="fa fa-phone-square"></i>
	<?php echo ListView::widget([
        'dataProvider' => $phoneDataProvider,
        'itemView'=>'_view-phone', 
    ]); ?>
	
		</div><br>
	<div class="clearfix"></div>
</div>
