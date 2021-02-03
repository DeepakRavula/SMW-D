<?php

use common\components\gridView\KartikGridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\Location;
use yii\helpers\ArrayHelper;
use common\models\Student;
use common\models\Enrolment;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
?>
    <?php  
        $columns = [];
        array_push($columns, [
            'headerOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
            'value' => 'date',
            'label' => 'Date'
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
            'value' => 'teacher',
            'headerOptions' => ['class' => 'text-left'],
            'label' => 'Teacher'
        ]);

        array_push($columns, [
            'label' => 'Amount',
            'value' => 'amount',
            'headerOptions' => ['class' => 'text-right', 'style' => 'text-align:right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'text-align:right']
        ]);

        array_push($columns, [
            'label' => 'Payment',
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency($data['payment']);
            },
            'headerOptions' => ['class' => 'text-right', 'style' => 'text-align:right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'text-align:right']
        ]);

        array_push($columns, [
            'value' => 'balance',
            'label' => 'Balance',
            'headerOptions' => ['class' => 'text-right', 'style' => 'text-align:right'],
            'contentOptions' => ['class' => 'text-right invoice-value', 'style' => 'text-align:right']
        ]);
    ?>

<?php Pjax::begin(['enablePushState' => false, 'id' => 'group-lesson-line-item-listing','timeout' => 6000,]); ?>
<?= GridView::widget([
        'options' => ['id' => 'group-lesson-line-item-grid'],
        'dataProvider' => $lessonLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => 'No Lessons Available!'
    ]); ?>
<?php Pjax::end(); ?>
