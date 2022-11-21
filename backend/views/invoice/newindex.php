<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Invoice;
use common\models\Location;
use common\components\gridView\KartikGridView;
use backend\models\search\InvoiceSearch;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\models\search\InvoiceSearch */


$this->title = 'Invoice'; ?>
<div class="clearfix"></div>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
        'id' => 'new-invoice-listing',
        'timeout' => 6000,
    ]) ?>
	<?php
	$locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
            $columns = [
                [
					'attribute' => 'number',
					'label' => 'Number',
					'contentOptions' => ['style' => 'width:100px'],
                    'value' => function ($data) {
                        return $data->getInvoiceNumber();
                    },
			'filterType'=>KartikGridView::FILTER_SELECT2,
                'filter'=>ArrayHelper::map(Invoice::find()->invoice()
			->location($locationId)->orderBy(['invoice_number' => SORT_ASC])
                ->all(), 'id', 'invoiceNumber'),
                'filterWidgetOptions'=>[
            'options' => [
                'id' => 'proformainvoice',
            ],
                    'pluginOptions'=>[
                        'allowClear'=>true,
            ],    
		      
        ],
                'filterInputOptions'=>['placeholder'=>'Number'],
                ],
                [
					'label' => 'Date',
                    'value' => function ($data) {
                        $date = Yii::$app->formatter->asDate($data->date);
                        return !empty($date) ? $date : null;
                    }
                ],
                [
		    'attribute' => 'customer',
                    'label' => 'Customer',
                    'value' => function ($data) {
                        return !empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
                    },
                ],
                [
                    'attribute' => 'student',
                    'label' => 'Student',
                    'value' => function ($data) {
                        return !empty($data->user && $data->user->student) ? $data->hasStudent() ? $data->getStudentName() : $data->user->getStudentsList() : null;
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
                    'label' => 'Status',
		    'attribute' => 'invoiceStatus',
		    'filter'=> InvoiceSearch::invoiceStatuses(),
                    'value' => function ($data) {
                        return $data->getStatus();
                    },
                    'contentOptions' => function ($data) {
                        $options = [];
                        $type = (int) $data->type === Invoice::TYPE_INVOICE ? 'invoice' : 'pro-forma-invoice';
                        Html::addCssClass($options, $type.'-'.strtolower($data->getStatus()));

                        return $options;
                    },
                ],
                [
                    'format' => 'currency',
                    'value' => function ($data) {
                        if ((int) $data->type === Invoice::TYPE_INVOICE) {
                            if ($data->isPaid()) {
                                return (round($data->total, 2) > 0.00 && round($data->total, 2) <= 0.09) || (round($data->total, 2) < 0.00 && round($data->total, 2) >= -0.09) ? 0.00  : round($data->total, 2) ;
                            } else {
                                return (round($data->invoiceBalance, 2) > 0.00 && round($data->invoiceBalance, 2) <= 0.09) || (round($data->invoiceBalance, 2) < 0.00 && round($data->invoiceBalance, 2) >= -0.09) ? 0.00  : round($data->invoiceBalance, 2) ;
                            }
                            
                        }
                    },
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => function ($data) {
                        $options = [];
                        $type = (int) $data->type === Invoice::TYPE_INVOICE ? 'invoice' : 'pro-forma-invoice';
                        Html::addCssClass($options, 'text-right');
                        Html::addCssClass($options, $type.'-'.strtolower($data->getStatus()));

                        return $options;
                    },
                    'enableSorting' => false,
                ],
                [
                    'format' => 'currency',
                    'label' => 'Total',
                    'value' => function ($data) {
                        return round($data->total, 2);
                    },
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'enableSorting' => false,
                ],
            ];

        ?>
	
    <?php echo KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => "Showing {begin} - {end} of {totalCount} items",
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['invoice/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
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
	<?php \yii\widgets\Pjax::end(); ?>
    </div>
    <script>
        $(document).off('click', '.remove-button').on('click', '.remove-button', function() {
        var dateRange = $("#invoicesearch-invoicedaterange").val();
        if (!$.isEmptyObject(dateRange)) {
            $("#invoicesearch-invoicedaterange").val('').trigger('change');
        }
    });
    </script>
