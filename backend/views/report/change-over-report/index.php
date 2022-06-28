<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;
use common\models\LocationDebt;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<?php echo $this->render('change-over-report', [
    'paidFutureLessondataProvider' => $paidFutureLessondataProvider,
    'paidPastLessondataProvider' => $paidPastLessondataProvider,
    'activeInvoicedataProvider' => $activeInvoicedataProvider,
    'inactiveInvoicedataProvider' => $inactiveInvoicedataProvider,
    'activeCustomerWithCreditdataProvider' => $activeCustomerWithCreditdataProvider,
    'inactiveCustomerWithCreditdataProvider' => $inactiveCustomerWithCreditdataProvider,
    'paidFutureLessonsSum' => $paidFutureLessonsSum,
    'paidPastLessonsSum' => $paidPastLessonsSum,
    'activeOutstandingInvoicesSum' => $activeOutstandingInvoicesSum,
    'inactiveOutstandingInvoicesSum' => $inactiveOutstandingInvoicesSum,
    'paidFutureLessonsCount' => $paidFutureLessonsCount,
    'paidPastLessonsCount' => $paidPastLessonsCount,
    'activeOutstandingInvoicesCount' => $activeOutstandingInvoicesCount,
    'inactiveOutstandingInvoicesCount' => $inactiveOutstandingInvoicesCount,
    'numberOfActiveCustomers' => $numberOfActiveCustomers,
    'numberOfEnrolments' => $numberOfEnrolments,
    ]); ?>

<script>
    $(document).on("click", "#print", function () {
        var url = '<?php echo Url::to(['print/change-over-report']); ?>';
        window.open(url, '_blank');
    });
</script>
