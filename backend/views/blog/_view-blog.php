  <!-- Box Comment -->
  <div class="box box-widget">
	<div class="box-header with-border">
	  <div class="user-block">
		<span class="username"><?= !empty($model->title) ? $model->title : null ?></span>
		<span class="description"><i class="fa fa-clock"></i>
			<?php
				$postDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
				echo $postDate->format('F j, Y');
			?>
		</span>
	  </div>
	  <!-- /.user-block -->
	  <div class="box-tools">
	  </div>
	  <!-- /.box-tools -->
	</div>
	<!-- /.box-header -->
	<div class="box-body p-10">
	  <?= !empty($model->content) ? $model->content : null ?>
	</div>
  </div>
 <!-- /.box -->