<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use yii\helpers\Url;
use common\models\Invoice;
use yii\bootstrap\ActiveForm;

?>

<?php
    $columns = [];

    array_push($columns, [
        'headerOptions' => ['style' => 'width:250px;text-align:left'],
        'contentOptions' => ['style' => 'width:250px;text-align:left'],
        'label' => 'Date',
        'value' => function ($data) {
            return  !empty($data->date) ? Yii::$app->formatter->asDate($data->date): null;
        }
    ]);

    array_push($columns, [
        'headerOptions' => ['style' => 'width:250px;text-align:left'],
        'contentOptions' => ['style' => 'width:250px;text-align:left'],
        'label' => 'Number',
        'value' => function ($data) {
            return $data->invoiceNumber;
        }
    ]);

    array_push($columns, [
        'headerOptions' => ['style' => 'width:250px;text-align:right'],
        'contentOptions' => ['style' => 'width:250px;text-align:right'],
        'label' => 'Amount',
        'value' => function ($data) {
            return !empty($data->total) ? Yii::$app->formatter->asCurrency(round($data->total, 2)) : null;
        }
    ]);

    array_push($columns, [
        'headerOptions' => ['style' => 'width:250px;text-align:right'],
        'contentOptions' => ['style' => 'width:250px;text-align:right'],
        'label' => 'Balance',
        'value' => function ($data) {
            return !empty($data->balance) ? (round($data->balance, 2) > 0.00 && round($data->balance, 2) <= 0.09) || (round($data->balance, 2) < 0.00 && round($data->balance, 2) >= -0.09) ? Yii::$app->formatter->asCurrency(round(0.00, 2)):Yii::$app->formatter->asCurrency(round($data->balance, 2)) : null;
        }
    ]);


?>

<?php $gridId = 'invoice-line-item-grid-mail'; $pjaxId = 'invoice-line-item-listing-mail'; $class = 'line-items-value invoice-line-items'; ?>
<?php Pjax::Begin(['id' => $pjaxId, 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'options' => ['id' => $gridId],
        'dataProvider' => $invoiceLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => $class],
        'emptyText' => 'No Invoices Available!'
    ]); ?>
<?php Pjax::end(); ?>