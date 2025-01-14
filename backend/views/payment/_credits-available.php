<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use common\models\Invoice;
use common\models\Payment;
use yii\bootstrap\ActiveForm;
$reference = $model->notes ? $model->notes : $model->reference ? $model->reference : "";
?>
<div id="lesson-grid" class="col-md-12">
<table class="table table-striped table-bordered">
    <thead>
        <tr class="bg-light-gray">
            <th class="text-left">Reference</th>
            <th class="text-left">Date</th>
            <th class="text-left">Payment Method</th>
            <th class="text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr class="line-items-value lesson-line-items" data-key="1">
            <td class="text-left"> <?= $reference ?> </td>
            <td class="text-left"> <?= (new DateTime($model->date))->format('M d, Y'); ?> </td>
            <td class="text-left"> <?= $model->paymentMethod->name ?> </td>
            <td class="text-right"><?= Yii::$app->formatter->asCurrency(round($model->amount, 2)); ?></td>
        </tr>
    </tbody>
</table>
</div>
