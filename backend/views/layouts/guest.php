<?php

/**
 * @var $this yii\web\View
 */
use backend\assets\BackendAsset;
use common\models\TimelineEvent;
use yii\helpers\Html;
use yii\helpers\Url;

$bundle = BackendAsset::register($this);
?>
<?php $this->beginContent('@backend/views/layouts/base.php'); ?>
<div class="wrapper">
<?php echo $content ?>
</div><!-- ./wrapper -->

<?php $this->endContent(); ?>