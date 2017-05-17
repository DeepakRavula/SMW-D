<style>
	@page{
		size: auto;
		margin: 3mm;
	}
	@media print {
		.invoice-view .invoice-status {
            float: right;
        }
        
        .invoice-view .invoice-status .invoice-number {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }
        
        .invoice-view .invoice-status b {
            margin: 0;
        }
		li {
		  list-style-type: none;
		}
		.invoice-view .logo>img {
			padding: 0;
			position: relative;
			left: -1px;
		}
		.invoice-print-address {
			width: 700px;
			margin-top: 10px;
		}
		.invoice-print-address ul {
			display: block;
			float: left;
			width: 45%;

		}
		.invoice-print-address h1 {
			margin: 0;
			padding: 0;
			text-transform: capitalize;
		}
		.invoice-print-address ul li {
			font-size: 16px;
			font-weight: 300;
			color: #000;
		}
		.invoice-info {
			margin-top: 15px;
		}
		.invoice-info .grid-view{
			clear:both;
			padding-top:10px;
		}
		.text-gray {
			color: gray !important;
		}
		.invoice-labels {
			width: 82px;
		}
		.text-left {
			text-align: left !important;
		}
		.reminder_notes {
			position: fixed;
			bottom: 0;
		}
	}
	.invoice-print-address ul li {
		font-size: 16px;
		font-weight: 300;
		color: #000;
	}
</style>
<div class="row-fluid" >
	<div class="logo invoice-col" style="width: 100%">
		<img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png" />
		<div class="invoice-status">
			<div class="invoice-col" style="width: 125px; text-align:right;">
				<p class="invoice-number" style="font-weight:700; font-size:16px;">
				<strong>Student</strong>
				</p>
				<p class="invoice-number" style="font-weight:700; font-size:16px;">
				<h4><strong><?= $studentModel->fullName; ?></strong></h4>
				</p>
			</div>
		</div>
	</div>
	<div class="invoice-col " style="clear: both;">
		<div class="invoice-print-address">
			<ul>
				<li><strong>Arcadia Music Academy ( <?= $studentModel->customer->userLocation->location->name; ?> )</strong></li>
				<li>
					<?php if (!empty($studentModel->customer->userLocation->location->address)): ?>
						<?= $studentModel->customer->userLocation->location->address; ?>
					<?php endif; ?>
				</li>
				<li>
					<?php if (!empty($studentModel->customer->userLocation->location->city_id)): ?>
						<?= $studentModel->customer->userLocation->location->city->name; ?>
					<?php endif; ?>
					<?php if (!empty($studentModel->customer->userLocation->location->province_id)): ?>
						<?= ', ' . $studentModel->customer->userLocation->location->province->name; ?>
					<?php endif; ?>
				</li>
				<li>
					<?php if (!empty($studentModel->customer->userLocation->location->postal_code)): ?>
						<?= $studentModel->customer->userLocation->location->postal_code; ?>
					<?php endif; ?>
				</li>
			</ul>
			<ul>
				<li>
					</br>
				</li>
				<li>
					<?php if (!empty($studentModel->customer->userLocation->location->phone_number)): ?>
						<?= $studentModel->customer->userLocation->location->phone_number ?>
					<?php endif; ?>
				</li>
				<li>
					<?php if (!empty($studentModel->customer->userLocation->location->email)): ?>
						<?= $studentModel->customer->userLocation->location->email ?>
					<?php endif; ?>
				</li>
				<li>
					www.arcadiamusicacademy.com
				</li>
			</ul>
		</div>
	</div>
	<div class="invoice-col" style="clear:both; ">
		<div class="invoice-print-address">
			<ul>
				<li>
					<strong>Customer</strong>
				</li>
				<li>
					<h1 class="m-0" style="font-size:14px;">
						<?php echo isset($studentModel->customer->publicIdentity) ? $studentModel->customer->publicIdentity : null ?>
					</h1>
				</li>
				<?php if (!empty($studentModel->customer->addresses)) : ?>
					<?php
					foreach ($studentModel->customer->addresses as $address) {
						if ($address->label === 'Billing') {
							$billingAddress = $address;
							break;
						}
					}
					?>
				<?php endif; ?>
				<?php $phoneNumber = !empty($studentModel->customer->phoneNumber) ? $studentModel->customer->phoneNumber : null;
				?>
				<li>
					<!-- Billing address -->
					<?php if (!empty($billingAddress->address)) : ?>
						<?= $billingAddress->address; ?>
					<?php endif; ?>
				</li>
				<li>                          
					<?php if (!empty($billingAddress->city->name)) : ?>
						<?= $billingAddress->city->name; ?>
					<?php endif; ?>                          
					<?php if (!empty($billingAddress->province->name)) : ?>
						<?= ', ' . $billingAddress->province->name; ?>
					<?php endif; ?>
				</li>
				<li>                           
					<?php if (!empty($billingAddress->postal_code)) : ?>
						<?= $billingAddress->postal_code; ?>
					<?php endif; ?>
				</li>
			</ul>
			<ul>
				<li>
					</br>
				</li>
				<li>
					<!-- Phone number -->
					<?php if (!empty($phoneNumber)) : ?>
						<?php echo $phoneNumber->number; ?>
					<?php endif; ?>
				</li>
				<li>
					<?php if (!empty($studentModel->customer->email)): ?>
						<?php echo $studentModel->customer->email ?>
					<?php endif; ?>
				</li>   
			</ul>
		</div>
	</div>
</div>