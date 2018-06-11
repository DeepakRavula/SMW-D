<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\search\PaymentSearch;
use kartik\grid\GridView;
use common\components\gridView\KartikGridView;
use yii\helpers\ArrayHelper;
use common\models\PaymentMethod;
use common\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payments';
?>
<?php
$locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $columns = [
                [
                    'label' => 'Date',
                    'attribute' => 'dateRange',
                'value' => function ($data) {
                    if (!empty($data->date)) {
                        $lessonDate = Yii::$app->formatter->asDate($data->date);
                        return $lessonDate;
                    }

                    return null;
                },
                'filterType' => KartikGridView::FILTER_DATE_RANGE,
        'filterWidgetOptions' => [
            'id' => 'enrolment-startdate-search',
            'convertFormat' => true,
            'initRangeExpr' => true,
            'pluginOptions' => [
                'autoApply' => true,
                'allowClear' => true,
                'ranges' => [
                    Yii::t('kvdrp', 'Last Month') => ["moment().subtract(1, 'month').startOf('month')",
                    "moment().subtract(1, 'month').endOf('month')"],
                    Yii::t('kvdrp', 'Last Week') => ["moment().subtract(1, 'week').startOf('week')",
                    "moment().subtract(1, 'week').endOf('week')"],
                    Yii::t('kvdrp', "Yesterday") => ["moment().startOf('day').subtract(1,'days')", "moment().endOf('day').subtract(1,'days')"],
                    Yii::t('kvdrp', "Today") => ["moment().startOf('day')", "moment()"],
                        Yii::t('kvdrp', 'This Week') => ["moment().startOf('week')",
                        "moment().endOf('week')"],
                    Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')",
                        "moment().endOf('month')"],
                   
                ],
                'locale' => [
                    'format' => 'M d, Y',
                ],
                'opens' => 'right',
            ],

        ],
                
            ],
            [
                'label' => 'Customer',
                'attribute' => 'customer',
                'value' => function ($data) {
                    return !empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
                },
                'contentOptions' => ['style' => 'font-size:14px'],
                'filterType'=>KartikGridView::FILTER_SELECT2,
                'filter'=> ArrayHelper::map(User::find()->customers($locationId)->notDeleted()->active()
                ->all(), 'id', 'publicIdentity'),
                'filterWidgetOptions'=>[
                    'pluginOptions'=>[
                        'allowClear'=>true,
            ],

        ],
                'filterInputOptions'=>['placeholder'=>'Customer'],
                'format'=>'raw'
                
            ],
                [
                'label' => 'Payment Method',
                'attribute' => 'paymentMethod',
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                },
                'filterType' => KartikGridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(PaymentMethod::find()
                ->andWhere(['displayed' => true])
                ->orderBy(['name' => SORT_ASC])
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