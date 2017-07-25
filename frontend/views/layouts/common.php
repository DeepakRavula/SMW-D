<?php

/**
 * @var $this yii\web\View
 */
use frontend\assets\FrontendAsset;
use backend\widgets\Menu;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

$bundle = FrontendAsset::register($this);
?>
<?php $this->beginContent('@frontend/views/layouts/base.php'); ?>
<div>
	<?= $this->render('_header'); ?>
		<?php echo $content ?>
</div><!-- ./wrapper -->

<?php $this->endContent(); ?>