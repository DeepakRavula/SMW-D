<?php

use yii\grid\GridView;
?>
<div class="row-fluid">
	<div class="logo invoice-col" style="width: 150px">              
		<img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
	</div>
	<div class="invoice-col text-gray" style="font-size:18px; width: 180px;">
		<small>
			<?php if (!empty($studentModel->customer->userLocation->location->address)): ?>
				<?= $studentModel->customer->userLocation->location->address ?><br>
			<?php endif; ?>
			<?php if (!empty($studentModel->customer->userLocation->location->phone_number)): ?>
				<?= $studentModel->customer->userLocation->location->phone_number ?>
			<?php endif; ?>
			<?php if (!empty($studentModel->customer->userLocation->location->email)): ?>
				<?= $studentModel->customer->userLocation->location->email ?>
			<?php endif; ?> 
		</small> 
	</div>
	<div class="invoice-col" style="width: 220px;">
		<strong>
			<?php echo isset($studentModel->fullName) ? $studentModel->fullName : null ?>
		</strong>
	</div>
	<div class="clearfix"></div>
</div>
<div>
	<?php
	echo GridView::widget([
		'dataProvider' => $examResultDataProvider,
		'options' => ['class' => 'col-md-12 p-0'],
		'tableOptions' => ['class' => 'table table-bordered'],
		'headerRowOptions' => ['class' => 'bg-light-gray'],
		'columns' => [
			[
				'label' => 'Exam Date',
				'value' => function($data) {
					return !empty($data->date) ? (new \DateTime($data->date))->format('M. d, Y') : null;
				}
			],
			[
				'label' => 'Mark',
				'value' => function($data) {
					return !empty($data->mark) ? $data->mark : null;
				}
			],
			[
				'label' => 'Level',
				'value' => function($data) {
					return !empty($data->level) ? $data->level : null;
				}
			],
			[
				'label' => 'Program',
				'value' => function($data) {
					return !empty($data->programId) ? $data->program->name : null;
				}
			],
			[
				'label' => 'Type',
				'value' => function($data) {
					return !empty($data->type) ? $data->type : 'None';
				}
			],
			[
				'label' => 'Teacher',
				'value' => function($data) {
					return !empty($data->teacherId) ? $data->teacher->publicIdentity : null;
				}
			],
		],
	]);
	?>
</div>
	<script>
        $(document).ready(function () {
            window.print();
        });
	</script>