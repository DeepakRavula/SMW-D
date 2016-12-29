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
	<section class="content">
		<?php echo $content ?>
	</section><!-- /.content -->
<?php $this->endContent(); ?>
