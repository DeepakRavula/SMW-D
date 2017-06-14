<?php

?>
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Schedule</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" ><i class="fa fa-pencil"></i></button>
		</div>
	</div>
	<div class="box-body">
			<div class="row">
				<div class="col-md-2">
					<strong>Date</strong>
				</div>
				<div class="col-md-5">
					<?= (new \DateTime($model->date))->format('l, F jS, Y'); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<strong>Time</strong>
				</div>
				<div class="col-md-4">
                    <?= Yii::$app->formatter->asTime($model->date); ?>
                  </a>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<strong>Duration</strong>
				</div>
				<div class="col-md-4">
                    <?= (new \DateTime($model->duration))->format('H:i'); ?>
                  </a>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<strong>Classroom</strong>
				</div>
				<div class="col-md-4">
                    <?= !empty($model->classroom->name) ? $model->classroom->name : 'None'; ?>
                  </a>
				</div>
			</div>
		</div>
</div>