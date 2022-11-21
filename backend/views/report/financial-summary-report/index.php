<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;
use common\models\LocationDebt;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<?php echo $this->render('financial-summary-report', [
    'paidPastGroupLessonsdataProvider' => $paidPastGroupLessonsdataProvider,
    'paidPastGroupLessonsCount' => $paidPastGroupLessonsCount,
    'paidPastGroupLessonsSum' => $paidPastGroupLessonsSum,
    'paidFutureGroupLessonsSearchModel' => $paidFutureGroupLessonsSearchModel,
    'paidFutureGroupLessonsdataProvider' => $paidFutureGroupLessonsdataProvider,
    'paidFutureLessonsSearchModel' => $paidFutureLessonsSearchModel,
    'paidFutureLessondataProvider' => $paidFutureLessondataProvider,
    'paidPastLessondataProvider' => $paidPastLessondataProvider,
    'activeInvoicedataProvider' => $activeInvoicedataProvider,
    'inactiveInvoicedataProvider' => $inactiveInvoicedataProvider,
    'activeCustomerWithCreditdataProvider' => $activeCustomerWithCreditdataProvider,
    'inactiveCustomerWithCreditdataProvider' => $inactiveCustomerWithCreditdataProvider,
    'paidPastLessonsSum' => $paidPastLessonsSum,
    'activeOutstandingInvoicesSum' => $activeOutstandingInvoicesSum,
    'inactiveOutstandingInvoicesSum' => $inactiveOutstandingInvoicesSum,
    'paidPastLessonsCount' => $paidPastLessonsCount,
    'activeOutstandingInvoicesCount' => $activeOutstandingInvoicesCount,
    'inactiveOutstandingInvoicesCount' => $inactiveOutstandingInvoicesCount,
    'numberOfActiveCustomers' => $numberOfActiveCustomers,
    'numberOfEnrolments' => $numberOfEnrolments,
    ]); ?>

<script>
    $(document).on("click", "#print", function () {
        var url = '<?php echo Url::to(['print/financial-summary-report']); ?>';
        window.open(url, '_blank');
    });
</script>
