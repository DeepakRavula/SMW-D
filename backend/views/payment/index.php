<?php

use common\components\gridView\KartikGridView;
use yii\helpers\ArrayHelper;
use common\models\PaymentMethod;
use common\models\User;
use common\models\Location;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payments';
$this->params['action-button'] = $this->render('_action-button');?>
<?php yii\widgets\Pjax::begin(['id' => 'payment-listing']); ?>

<?php
    $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
    $columns = [
        [
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
            'headerOptions' => ['class' => 'text-left', 'style' => 'width:20%'],
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
                return $data->invoice->user->publicIdentity ?? null;
            },
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:35%'],
            'headerOptions' => ['class' => 'text-left', 'style' => 'width:35%'],
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
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:30%'],
            'headerOptions' => ['class' => 'text-left', 'style' => 'width:30%'],
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
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:25%'],
            'headerOptions' => ['class' => 'text-right', 'style' => 'width:25%'],
        ],
    ];
?>

    <div>
        <?= KartikGridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'options' => ['class' => ''],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
            'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed', 'id' => 'payment'],
            'columns' => $columns,
        ]); ?>
    </div>
<?php yii\widgets\Pjax::end(); ?>
<script>
  $(document).on('click', '#payment-listing  tbody > tr', function () {
            var paymentId = $(this).data('key');
            var customUrl = '<?= Url::to(['payment/update-payment']); ?>?id=' + paymentId;
            $.ajax({
                url    : customUrl,
                type   : 'get',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#modal-content').html(response.data);
                        $('#popup-modal').modal('show');
                       
                    }
                }
            });
            return false;
        });
</script>