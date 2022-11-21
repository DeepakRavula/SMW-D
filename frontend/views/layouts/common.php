<?php

/**
 * @var $this yii\web\View
 */
use frontend\assets\FrontendAsset;

$bundle = FrontendAsset::register($this);
?>
<?php $this->beginContent('@frontend/views/layouts/base.php'); ?>
<div>
	<?= $this->render('_header'); ?>
		<?php echo $content ?>
</div><!-- ./wrapper -->

<?php $this->endContent(); ?>