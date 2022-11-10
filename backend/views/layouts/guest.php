<?php
/**
 * @var yii\web\View
 */
use backend\assets\BackendAsset;

$bundle = BackendAsset::register($this);
?>
<?php $this->beginContent('@backend/views/layouts/base.php'); ?>
	<section class="content-header">
		<h1>
			<?php echo $this->title ?>
			<div class="clearfix"></div>
		</h1>
	</section>
	<!-- Main content -->
	<div class="box">
    <div class="box-body">
	<section class="content">
		<?php echo $content ?>
	</section><!-- /.content -->
	  </div>
</div>
<?php $this->endContent(); ?>