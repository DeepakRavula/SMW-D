<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="col-md-12 p-b-20">
    <div class="row">
        <div class="pull-right">
        <?= Html::a('<i class="fa fa-print"></i>', ['print/customer-account', 'id' => $userModel->id], ['id' => 'account-print', 'target' => '_blank']) ?>
        </div>
        </div>   
    <div>
 <?php
    yii\widgets\Pjax::begin([
        'id' => 'accounts-customer',
        'timeout' => 6000,
    ])

    ?>  
    <?php
    echo GridView::widget([
        'dataProvider' => $accountDataProvider,
        'summary' =>'',
        'tableOptions' => ['class' => 'table table-bordered m-0'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
                [
                'label' => 'Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date);
                }
            ],
                [
                'headerOptions' => ['class' => 'text-left'],
                'contentOptions' => ['class' => 'text-left'],
                'label' => 'Description',
                'value' => function ($data) {
                    return $data->getAccountDescription();
                }
            ],
                [
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'label' => 'Debit',
                'value' => function ($data) {
                    return !empty($data->debit) ? Yii::$app->formatter->asCurrency($data->debit) : null;
                }
            ],
                [
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'label' => 'Credit',
                'value' => function ($data) {
                    return !empty($data->credit) ? Yii::$app->formatter->asCurrency($data->credit) : null;
                }
            ],
                [
                'format' => ['decimal', 2],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'label' => 'Balance',
                'value' => function ($data) {
                    return $data->balance;
                }
            ]
        ],
    ]);

    ?>
    </div>
</div>
<?php \yii\widgets\Pjax::end(); ?>

