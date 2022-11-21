<?php
/**
 * @var yii\web\View
 */
use backend\assets\BackendAsset;

$bundle = BackendAsset::register($this);
?>
<?php $this->beginContent('@backend/views/layouts/base.php'); ?>
<div class="container invoice">
<div class="col-md-12">
	<?php echo $content ?>
</div>
</div>
<?php $this->endContent(); ?>