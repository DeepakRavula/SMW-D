<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Invoice;
use common\components\gridView\KartikGridView;
use backend\models\search\InvoiceSearch;
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
	<?php
        if ((int) $searchModel->type === (int) Invoice::TYPE_PRO_FORMA_INVOICE) {
            $columns = [
                [
					'attribute' => 'number',
					'label' => 'Number',
					'contentOptions' => ['style' => 'width:100px'],
                    'value' => function ($data) {
                        return $data->getInvoiceNumber();
                    },
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
                            'format' => 'M d,Y',
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
		    'attribute' => 'invoiceStatus',
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
                            return Yii::$app->formatter->asDecimal($data->total);
                        } else {
                            return Yii::$app->formatter->asDecimal($data->invoiceBalance);
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
                        return Yii::$app->formatter->asDecimal($data->total);
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
                ],
                [
					'attribute' => 'invoiceDateRange',
					'label' => 'Date',
					'filterType' => KartikGridView::FILTER_DATE_RANGE,
			  'filterWidgetOptions' => [
                    'model' => $searchModel,
                    'convertFormat' => true,
                    'initRangeExpr' => true,
                    'attribute' => 'invoiceDateRange',
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
                            'format' => 'M d,Y',
                        ],
                        'opens' => 'right'
                    ],
                ],
                    'value' => function ($data) {
                        $date = Yii::$app->formatter->asDate($data->date);

                        return !empty($date) ? $date : null;
                    },
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
                                return Yii::$app->formatter->asDecimal($data->total);
                            } else {
                                return Yii::$app->formatter->asDecimal($data->invoiceBalance);
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
                        return Yii::$app->formatter->asDecimal($data->total);
                    },
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'enableSorting' => false,
                ],
            ];
        }

        ?>
	<?php yii\widgets\Pjax::begin([
        'id' => 'invoice-listing',
        'timeout' => 6000,
    ]) ?>
    <?php echo KartikGridView::widget([
        'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'summary' => false,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['invoice/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'columns' => $columns,
    ]); ?>
	<?php \yii\widgets\Pjax::end(); ?>
    </div>
