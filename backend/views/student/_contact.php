<div class="row-fluid p-b-20">
	<div class="col-md-6">
		<div class="row-fluid">
			<i class="fa fa-map-marker"></i>
			<p>Address </p>
			<table class="table">
				<tbody>
					<?php
					$addresses = $model->customer->addresses;
					$phoneNumbers = $model->customer->phoneNumbers;
					foreach ($addresses as $address) {
						echo '<tr><td class="width-first-column">' . $address->label . '</td><td>'
						. $address->address . '<br>'
						. $address->city->name . ', '
						. $address->province->name . '  '
						. $address->postal_code . '<br>'
						. $address->country->name;

//						if ($address->id == $model->primary_address_id)
//							echo ' (Default)';
						echo '</td><td>';
//						echo CHtml::link('<i class="fa fa-times"></i>', array('deleteAttr', 'id_user' => $model->id, 'id_addr' => $address->id), array('class' => 'delete_addr', 'confirm' => 'Are you sure?'));
						echo '</td>';
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
 <?php //echo!empty($address->address) ? $address->address : null ?>
		</div>
	</div>
	<div class="col-md-6">
		<div class="row-fluid">
			<p class="m-0">Phone number</p>
			<i class="fa fa-phone-square"></i>
			<table class="table">
				<tbody>
					<?php
					foreach ($phoneNumbers as $phoneNumber) {
						echo '<tr><td class="width-first-column">' . $phoneNumber->label->name . '</td><td>'
						. $phoneNumber->number;
						if (isset($phoneNumber->extension))
							echo ', '.$phone_item->extension;

//						if ($address->id == $model->primary_address_id)
//							echo ' (Default)';
						echo '</td><td>';
//						echo CHtml::link('<i class="fa fa-times"></i>', array('deleteAttr', 'id_user' => $model->id, 'id_addr' => $address->id), array('class' => 'delete_addr', 'confirm' => 'Are you sure?'));
						echo '</td>';
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
		</div>
		<div class="row-fluid m-t-10">
			<p class="m-0">Email</p>
			<i class="fa fa-envelope"></i> <?php echo!empty($model->customer->email) ? $model->customer->email : null ?>
		</div>
	</div>
	<div class="clearfix"></div>
</div>