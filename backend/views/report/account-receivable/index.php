<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;
use common\models\LocationDebt;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Accounts Receivable';
$this->params['action-button'] = Html::a('<i class="fa fa-print"></i>', '#', ['id' => 'print', 'class' => 'btn btn-box-tool']);
?>
<?php echo $this->render('account-receivable', ['dataProvider' => $dataProvider,]); ?>

<script>
    $(document).on("click", "#print", function () {
        var url = '<?php echo Url::to(['print/accounts-receivable']); ?>';
        window.open(url, '_blank');
    });
</script>