<?php

use common\components\gridView\KartikGridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\Location;
use yii\helpers\ArrayHelper;
use common\models\Student;
use yii\bootstrap\Html;

?>

<?php Pjax::begin(['enablePushState' => false, 'id' => 'lesson-line-item-listing', 'timeout' => 6000]); ?>
    <?php  
        $columns = [];
        array_push($columns, [
            'headerOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
            'attribute' => 'dateRange',
            'label' => 'Date',
            'value' => 'date'
        ]);

        array_push($columns, [
            'label' => 'Student',
            'value' => 'student'
        ]);

        array_push($columns, [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'value' => 'program',
            'label' => 'Program'
        ]);

        array_push($columns, [
            'headerOptions' => ['class' => 'text-left'],
            'label' => 'Teacher',
            'value' => 'teacher'
        ]);

        array_push($columns, [
            'label' => 'Amount',
            'value' => 'amount',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ]);

        array_push($columns, [
            'value' => 'payment',
            'label' => 'Payment',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right invoice-value']
        ]);

        array_push($columns, [
            'value' => 'balance',
            'label' => 'Balance',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right invoice-value']
        ]);

    ?>
<?php if ($searchModel->showCheckBox) : ?>
    <?= GridView::widget([
        'options' => ['id' => 'lesson-line-item-grid'],
        'dataProvider' => $lessonLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => 'line-items-value lesson-line-items'],
        'emptyText' => 'No Lessons Available!'
    ]); ?>
<?php else: ?>
<?= GridView::widget([
        'options' => ['id' => 'lesson-line-item-grid'],
        'dataProvider' => $lessonLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => 'No Lessons Available!'
    ]); ?>
<?php endif; ?>
<?php Pjax::end(); ?>
