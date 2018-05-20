<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\search\PaymentSearch;
use kartik\grid\GridView;
use common\components\gridView\KartikGridView;
use yii\helpers\ArrayHelper;
use common\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payments';
?>
<?php
        $columns = [
                [
                    'label' => 'Date',
                    'attribute' => 'date',
                'value' => function ($data) {
                    if (!empty($data->date)) {
                        $lessonDate = Yii::$app->formatter->asDate($data->date);
                        return $lessonDate;
                    }

                    return null;
                },
                'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left','class'=>'main-group'],
                
            ],
            [
                'label' => 'Customer',
                'attribute' => 'customer',
                'value' => function ($data) {
                    return !empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
                },
                'contentOptions' => ['style' => 'font-size:14px'],
                
            ],
                [
                'label' => 'Payment Method',
                'attribute' => 'paymentMethod',
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                },
                'filterType' => KartikGridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(PaymentMethod::find()->orderBy(['name' => SORT_ASC])
                ->asArray()->all(), 'name', 'name'),
                'filterWidgetOptions'=>[
           
                    'pluginOptions'=>[
                        'allowClear'=>true,
            ],

        ],
                'filterInputOptions'=>['placeholder'=>'Payment Method'],
                'format'=>'raw'
            ],
            
            [
                'label' => 'Amount',
                'attribute' => 'amount',
                'value' => function ($data) {
                    $amount = abs($data->amount);
                    return Yii::$app->formatter->asDecimal($amount,2);
                },
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px'],
                'headerOptions' => ['class' => 'text-right'],

            ],
        ];
        ?>

<div class="grid-row-open">
        <?=
            KartikGridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'options' => ['class' => ''],
                'headerRowOptions' => ['class' => 'bg-light-gray'],
                'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel) {
                        $url = Url::to(['invoice/view', 'id' => $model->invoice->id]);
                        $data = ['data-url' => $url];
                        return $data;
                },
                'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed', 'id' => 'payment'],
                'columns' => $columns,
            ]);
            ?>
    </div>