<?php
use common\models\User;
use yii\helpers\Url;

?>
<div class="box box-default">
          <div class="box-header with-border">
              <h3 class="box-title">Details</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool"><i class="fa fa-pencil"></i></button>
              </div>
            </div>
		<div class="box-body">
			<div class="row">
				<div class="col-md-2">
					<strong>Program</strong>
				</div>
				<div class="col-md-4">
					<?= $model->course->program->name; ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<strong>Teacher</strong>
				</div>
				<div class="col-md-4">
					<a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => User::ROLE_TEACHER, 'id' => $model->teacherId]) ?>">
                    <?= $model->teacher->publicIdentity; ?>
                  </a>
				</div>
			</div>
		</div>
	</div>
	