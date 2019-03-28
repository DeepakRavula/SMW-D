<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use common\models\Invoice;
use common\models\Payment;
use yii\bootstrap\ActiveForm;
?>


<?php
    $columns = [];

    array_push($columns, [
        'headerOptions' => ['style' => 'width:400px;text-align:left'],
        'contentOptions' => ['style' => 'width:400px;text-align:left'],
        'label' => 'Type',
        'value' => 'type',
    ]);

    array_push($columns, [
        'headerOptions' => ['style' => 'width:300px;text-align:left'],
        'contentOptions' => ['style' => 'width:300px;text-align:left'],
        'label' => 'Reference',
        'value' => 'reference',
    ]);


    array_push($columns, [
        'format' => 'currency',
        'headerOptions' => ['style' => 'width:300px;text-align:right'],
        'contentOptions' => ['style' => 'width:300px;text-align:right'],
        'label' => 'Amount',
        'value' => 'amount'
    ]);

?>

<?php Pjax::Begin(['id' => 'credit-lineitem-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'id' => 'credit-line-item-grid',
        'dataProvider' => $creditDataProvider,
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => 'credit-items-value'],
        'emptyText' => 'No Credits Available!'
    ]); ?>
<?php Pjax::end(); ?>
