<div class="row-fluid p-b-20">
	<div class="col-md-6">
		<div class="row-fluid">
			<p>Address 1</p>
			<i class="fa fa-map-marker"></i> <?php echo!empty($address->address) ? $address->address : null ?>
		</div>
	</div>
	<div class="col-md-6">
		<div class="row-fluid">
			<p class="m-0">Phone number</p>
			<i class="fa fa-phone-square"></i> <?php echo!empty($model->phoneNumber->number) ? $model->phoneNumber->number : null ?>
		</div>
		<div class="row-fluid m-t-10">
			<p class="m-0">Email</p>
			<i class="fa fa-envelope"></i> <?php echo!empty($model->email) ? $model->email : null ?>
		</div>
	</div>
	<div class="clearfix"></div>
</div>