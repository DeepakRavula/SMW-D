<?php
/**
 * @var $this yii\web\View
 */
use backend\assets\BackendAsset;
use backend\widgets\Menu;
use common\models\TimelineEvent;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use common\models\User;
use common\models\Location;
use yii\web\JsExpression;

$bundle = BackendAsset::register($this);
?>
<?php $this->beginContent('@backend/views/layouts/base.php'); ?>
<div class="container invoice">
<div class="col-md-12">
	<?php echo $content ?>
</div>
</div>

<?php $this->endContent(); ?>