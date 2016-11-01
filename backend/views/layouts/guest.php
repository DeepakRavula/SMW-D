<?php

/**
 * @var yii\web\View
 */
use backend\assets\BackendAsset;

$bundle = BackendAsset::register($this);
?>
<?php $this->beginContent('@backend/views/layouts/base.php'); ?>
<div class="wrapper">
<?php echo $content ?>
</div><!-- ./wrapper -->

<?php $this->endContent(); ?>