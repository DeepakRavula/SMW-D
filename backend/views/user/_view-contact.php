<?php

use yii\helpers\Html;
use yii\widgets\ListView;
?>
<div class="row p-10">
	<div class="col-xs-4">
		<div class="row-fluid">
		<p class="c-title m-0"><i class="fa fa-map-marker"></i> Addresses </p>
		  <?php echo ListView::widget([
                'dataProvider' => $addressDataProvider,
                'itemView' => '_view-contact-address',
			  	'summary' => ''
            ]); ?>
		</div>
	</div>
	<div class="col-xs-5">
		<div class="row-fluid">
			<p class="c-title m-0"><i class="fa fa-phone-square"></i> Phone Numbers</p>
			<?php echo ListView::widget([
                'dataProvider' => $phoneDataProvider,
                'itemView' => '_view-contact-phone',
			  	'summary' => ''
            ]); ?>
		</div>
	</div>
	
	<div class="clearfix"></div>
	<div class="col-xs-12">
		<?php echo Html::a('<i class="fa fa-pencil"></i> Edit Contact Information', ['update', 'UserSearch[role_name]' => $searchModel->role_name, 'id' => $model->id, '#' => 'contact'], ['class' => 'm-R-20']) ?>
	</div>
</div>
