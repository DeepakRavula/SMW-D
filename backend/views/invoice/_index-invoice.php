<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Invoice;
use common\components\gridView\AdminLteGridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\models\search\InvoiceSearch */

$invoiceAddButton = Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Add', ['invoice/blank-invoice'], ['class' => 'btn btn-primary btn-sm']);

$actionButton = (int) $searchModel->type === Invoice::TYPE_INVOICE ?  $invoiceAddButton : null;

$this->title = (int) $searchModel->type === Invoice::TYPE_PRO_FORMA_INVOICE ? 'Pro-forma Invoices' : 'Invoices';
$this->params['action-button'] = $actionButton; ?>
<?php if ((int)$searchModel->type === Invoice::TYPE_INVOICE) :?>
<?php $this->params['show-all'] = Html::a('<i class="fa fa-dollar"></i> Invoice All Completed Lessons', ['all-completed-lessons'], ['class' => 'btn btn-default btn-sm m-r-10']); ?> 
<?php endif;?>
<?php echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="clearfix"></div>
<div class="grid-row-open">
	<?php 
        if ((int) $searchModel->type === (int) Invoice::TYPE_PRO_FORMA_INVOICE) {
            $columns = [
                [
		'attribute' => 'number',
		'label' => 'Number',
                    'value' => function ($data) {
                        return $data->getInvoiceNumber();
                    },
                ],
                [
		    'attribute' => 'dueDate',
                    'label' => 'Due Date',
                    'value' => function ($data) {
                        $date = Yii::$app->formatter->asDate($data->dueDate);

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
                    'label' => 'Sent?',
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
                    'value' => function ($data) {
                        return $data->getInvoiceNumber();
                    },
                ],
                [
		'attribute' => 'date',
                'label' => 'Date',
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
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
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
