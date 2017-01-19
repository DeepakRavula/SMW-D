<?php

use yii\grid\GridView;
?>
<div>
	<h3 class="m-0"> <?= $studentModel->fullName; ?> </h3>
</div>
<div class="clearfix"></div>
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
					return !empty($data->program) ? $data->program : null;
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