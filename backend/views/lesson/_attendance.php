<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Attendance</h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool"><i class="fa fa-pencil"></i></button>
				</div>
            </div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-2">
						<strong>Present</strong>
					</div>
					<div class="col-md-4">
						<?= $model->getPresent(); ?>	
					</div>
				</div>
			</div>
		</div>