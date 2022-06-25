<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;
use common\models\LocationDebt;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<?php echo $this->render('amount-transfer-report', [
    'paidFutureLessondataProvider' => $paidFutureLessondataProvider,
    'paidPastLessondataProvider' => $paidPastLessondataProvider,
    'invoicedataProvider' => $invoicedataProvider,
    'customerWithCreditdataProvider' => $customerWithCreditdataProvider,
    ]); ?>

<script>
    $(document).on("click", "#print", function () {
        var url = '<?php echo Url::to(['print/amount-transfer-report']); ?>';
        window.open(url, '_blank');
    });
</script>
