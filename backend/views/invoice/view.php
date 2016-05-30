<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-view">
    <div>
        <div>
            <?php echo isset($model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->publicIdentity) ? $model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->publicIdentity : null?>
        </div>

        <div>
            <p>Date : <?php echo date("m/d/Y", strtotime($model->date));?> </p>
            <p>Number : <?php echo $model->invoice_number;?> </p>
            <p>Status : <?php echo $model->status($model);?> </p>
        </div>
    </div>
    <div>
    <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
            'dataProvider' => $invoiceLineItemsDataProvider,
            'columns' => [
                'id',
                'unit',
                'amount:currency',
            ],
        ]); ?>
    <?php yii\widgets\Pjax::end(); ?>
    </div>
    <div>
        <div>
            Payments
        </div>
        <div>
            <p>SubTotal : <?php echo $model->subTotal;?> </p> 
            <p>Tax : <?php echo $model->tax;?> </p>
            <p>Total : <?php echo $model->total;?> </p>
            <p>Paid : 0.00 </p>
            <p>Balance : <?php echo $model->total;?> </p>
        </div>
    </div>

    
</div>
