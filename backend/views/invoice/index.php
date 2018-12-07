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

$invoiceAddButton = Html::a('<i class="fa fa-plus" aria-hidden="true"></i>', ['invoice/blank-invoice'], ['class' => '']);

$actionButton = (int) $searchModel->type === Invoice::TYPE_INVOICE ?  $invoiceAddButton : null;

$this->title = (int) $searchModel->type === Invoice::TYPE_PRO_FORMA_INVOICE ? 'Pro-forma Invoices' : 'Invoices';
$this->params['action-button'] = $actionButton; ?>
<div class="clearfix"></div>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
        'id' => 'invoice-listing',
        'timeout' => 6000,
    ]) ?>
	<?php
	$locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        if ((int) $searchModel->type === (int) Invoice::TYPE_PRO_FORMA_INVOICE) {
            $columns = [
                [
					'attribute' => 'number',
					'label' => 'Number',
					'contentOptions' => ['style' => 'width:100px'],
                    'value' => function ($data) {
                        return $data->getInvoiceNumber();
                    },
	'filterType'=>KartikGridView::FILTER_SELECT2,
                'filter'=>ArrayHelper::map(Invoice::find()->proFormaInvoice()
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
		'attribute' => 'dateRange',
                    'label' => 'Due Date',
                    'value' => function ($data) {
                        $date = Yii::$app->formatter->asDate($data->dueDate);
                        return !empty($date) ? $date : null;
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
                    'label' => 'Sent?',
					'attribute' => 'mailStatus',
					'filter'=> InvoiceSearch::mailStatuses(),
                       'value' => function ($data) {
                           return $data->isSent ? 'Yes' : 'No';
                       },
                ],
                [
                    'label' => 'Paid?',
                       'value' => function ($data) {
                           return $data->isPaid() ? 'Yes' : 'No';
                       },
                ],
                   [
                    'label' => 'Status',
		    'attribute' => 'proFormaInvoiceStatus',
		    'filter'=> InvoiceSearch::proFormInvoiceStatuses(),
                    'value' => function ($data) {
                        return $data->getStatus();
                    },
                    'contentOptions' => function ($data) {
                        $options = [];
                        Html::addCssClass($options, 'invoice-'.strtolower($data->getStatus()));

                        return $options;
                    },
                ],
                [
                    'format' => 'currency',
                    'value' => function ($data) {
                        if ($data->isPaid()) {
                            return round($data->total, 2);
                        } else {
                            return round($data->invoiceBalance, 2);
                        }
                    },
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => function ($data) {
                        $options = [];
                        Html::addCssClass($options, 'text-right');
                        Html::addCssClass($options, 'invoice-'.strtolower($data->getStatus()));

                        return $options;
                    },
                    'enableSorting' => false,
                ],
                [
                    'label' => 'Total',
                    'format' => 'currency',
                    'value' => function ($data) {
                        return round($data->total, 2);
                    },
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'enableSorting' => false,
                ],
            ];
        } else {
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
					'attribute' => 'invoiceDateRange',
					'label' => 'Date',
                    'filter' => '<div class="input-group drp-container">'. DateRangePicker::widget([
                    'model' => $searchModel,
                    'convertFormat' => true,
                    'initRangeExpr' => true,
                    'attribute' => 'invoiceDateRange',
                        'options' => [
                            'class' => 'form-control',
                            'readOnly' => true,
                        ],
                        'pluginOptions' => [
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
                        ]
                    ]) . '<span class="input-group-addon remove-button" title="Clear field"><span class="glyphicon glyphicon-remove" ></span></span></div>',
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
        }

        ?>
	
    <?php echo KartikGridView::widget([
        'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
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
