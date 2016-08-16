<?php

use yii\helpers\Html;
use yii\widgets\ListView;
?>
<div class="row-fluid p-20">
	<div class="col-xs-4">
		<div class="row-fluid">
		<p class="c-title m-0"><i class="fa fa-map-marker"></i> Addresses </p>
		  <?php echo ListView::widget([
		        'dataProvider' => $addressDataProvider,
		        'itemView'=>'_view-contact-address', 
		    ]); ?>
		</div>
	</div>
	<div class="col-xs-4">
		<div class="row-fluid">
			<p class="c-title m-0"><i class="fa fa-phone-square"></i> Phone Numbers</p>
			<?php echo ListView::widget([
		        'dataProvider' => $phoneDataProvider,
		        'itemView'=>'_view-contact-phone', 
		    ]); ?>
		</div>
		<hr>
		<div class="row-fluid m-t-10 m-b-20">
			<div class="col-xs-2 p-0 c-title"><i class="fa fa-envelope"></i>  Email</p></div>
			<div class="col-xs-3"><?php echo!empty($model->email) ? $model->email : null ?></div>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="m-t-20">
		<?php echo Html::a('<i class="fa fa-pencil"></i> Edit Contact Information', ['update','UserSearch[role_name]' => $searchModel->role_name, 'id' => $model->id,'#' => 'contact'], ['class' => 'm-r-20']) ?>
	</div>
</div>
