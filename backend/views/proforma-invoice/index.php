<?php

use yii\helpers\Html;
use common\components\gridView\KartikGridView;
use yii\helpers\Url;
use common\models\Location;
use yii\widgets\Pjax;
use backend\models\search\ProformaInvoiceSearch;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\PrivateLessonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Requests';
$this->params['breadcrumbs'][] = $this->title;
$this->params['show-all'] = $this->render('_button', [
    'searchModel' => $searchModel
]);
?>
  <?php $locationId = Location::findOne(['slug' => \Yii::$app->location])->id; ?>
<?php  
        $columns = [];
        $columns = [
            [
                'attribute' => 'number',
                'label' => 'Number',
                'contentOptions' => ['style' => 'width:100px'],
                'value' => function ($data) {
                    return $data->getProformaInvoiceNumber();
                },
            ],
            [
                'label' => 'Due Date',
                'headerOptions' => ['class' => 'text-left'],
                'attribute' => 'dateRange',
	            'contentOptions' => ['class' => 'text-left'],
                'value' => function ($data) {
                    return !empty($data->dueDate) ? Yii::$app->formatter->asDate($data->dueDate) : null;
                },
                'filterType' => KartikGridView::FILTER_DATE_RANGE,
			  'filterWidgetOptions' => [
                    'model' => $searchModel,
                    'convertFormat' => true,
                    'initRangeExpr' => true,
                    'attribute' => 'dateRange',
                    'convertFormat' => true,
                    'pluginOptions'=>[
                        'autoApply' => true,
                        'ranges' => [
                            Yii::t('kvdrp', 'Today') => ["moment().startOf('day')", "moment()"],
                            Yii::t('kvdrp', 'Tomorrow') => ["moment().startOf('day').add(1,'days')", "moment().endOf('day').add(1,'days')"],
                            Yii::t('kvdrp', 'Next {n} Days', ['n' => 7]) => ["moment().startOf('day')", "moment().endOf('day').add(6, 'days')"],
                            Yii::t('kvdrp', 'Next {n} Days', ['n' => 30]) => ["moment().startOf('day')", "moment().endOf('day').add(29, 'days')"],
                        ],
                        'locale' => [
                            'format' => 'M d, Y',
                        ],
                        'opens' => 'right'
                    ],
                ],
            ],

            [
                'attribute' => 'customer',
                'label' => 'Customer',
                'value' => function ($data) {
                    return !empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
                },
            ],
            [
                'attribute' => 'phone',
                'label' => 'Phone',
                'value' => function ($data) {
                    return !empty($data->user->phoneNumber->number) ? $data->user->phoneNumber->number : null;
                },
            ],
            [
                'label' => 'Total',
		'headerOptions' => ['class' => 'text-right'],
	        'contentOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    return Yii::$app->formatter->asCurrency($data->total);
                },
            ],
        ];
        if ($searchModel->showAll) {
            array_push($columns,  [
                'label' => 'Status',
        'attribute' => 'proformaInvoiceStatus',
        'filter'=> ProformaInvoiceSearch::ProformaInvoiceStatuses(),
                'value' => function ($data) {
                    return $data->getPRStatus();
                },
                'contentOptions' => function ($data) {
                    $options = [];
                    $type = 1;
                    Html::addCssClass($options, $type.'-'.strtolower($data->getPrStatus()));
    
                    return $options;
                },
            ]);
        }

        ?>
<div class="grid-row-open">
<?php Pjax::begin([
    'timeout' => 8000,
    'enablePushState' => false,
    'id' => 'proforma-invoice-listing',]); ?>
    <?php echo KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'proforma-invoice-grid',
        'filterModel' => $searchModel,
        'summary' => "Showing {begin} - {end} of {totalCount} items",
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['proforma-invoice/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'emptyText' => false,
        'columns' => $columns,
        'toolbar' =>  [
            '{export}',
            '{toggleData}'
        ],
        'export' => [
            'fontAwesome' => true,
        ],  
        'panel' => [
            'type' => GridView::TYPE_DEFAULT
        ],
        'toggleDataOptions' => ['minCount' => 20],
    ]); ?>
</div>
    <?php Pjax::end(); ?>
    <script>
$(document).off('change', '#proformainvoicesearch-showall').on('change', '#proformainvoicesearch-showall', function () {
    var showAll = $(this).is(":checked");
    var number = $("input[name*='ProformaInvoiceSearch[number]").val();
    var customer = $("input[name*='ProformaInvoiceSearch[customer]").val();
    var phone = $("input[name*='ProformaInvoiceSearch[phone]").val();
    var dateRange = $("input[name*='ProformaInvoiceSearch[dateRange]").val();
    var params = $.param({ 'ProformaInvoiceSearch[dateRange]' :dateRange, 'ProformaInvoiceSearch[number]':number, 'ProformaInvoiceSearch[customer]':customer, 'ProformaInvoiceSearch[phone]':phone, 'ProformaInvoiceSearch[showAll]': (showAll | 0)});
    var url = "<?php echo Url::to(['proforma-invoice/index']); ?>?"+params;
    $.pjax.reload({url: url, container: "#proforma-invoice-listing", replace: false, timeout: 4000});  //Reload GridView
});
  </script>
