<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\Invoice;
use backend\models\search\InvoiceSearch;
use yii\widgets\ActiveForm;
use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\models\search\InvoiceSearch */

$invoiceAddButton = Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Add', ['invoice/blank-invoice'], ['class' => 'btn btn-success btn-sm']);

$actionButton = (int) $searchModel->type === Invoice::TYPE_INVOICE ?  $invoiceAddButton : null;

$this->title = (int) $searchModel->type === Invoice::TYPE_PRO_FORMA_INVOICE ? 'Pro-forma Invoices' : 'Invoices';
$this->params['action-button'] = $actionButton;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-index p-10">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
	<?php 
        if ((int) $searchModel->type === (int) Invoice::TYPE_PRO_FORMA_INVOICE) {
            $columns = [
                [
                'label' => 'Invoice Number',
                    'value' => function ($data) {
                        return $data->getInvoiceNumber();
                    },
                ],
                [
                    'label' => 'Date',
                    'value' => function ($data) {
                        $date = Yii::$app->formatter->asDate($data->date);

                        return !empty($date) ? $date : null;
                    },
                ],
                [
                    'label' => 'Due Date',
                    'value' => function ($data) {
                        $date = Yii::$app->formatter->asDate($data->dueDate);

                        return !empty($date) ? $date : null;
                    },
                ],
                [
                    'label' => 'Customer',
                    'value' => function ($data) {
                        return !empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
                    },
                ],
                [
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
                    'value' => function ($data) {
                        if ((int) $data->type === Invoice::TYPE_INVOICE) {
                            if ($data->status === 'Paid') {
                                return $data->total;
                            } else {
                                return $data->invoiceBalance;
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
                    'label' => 'Total',
                    'value' => function ($data) {
                        return $data->total;
                    },
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'enableSorting' => false,
                ],
            ];
        } else {
            $columns = [
                [
                'label' => 'Invoice Number',
                    'value' => function ($data) {
                        return $data->getInvoiceNumber();
                    },
                ],
                [
                'label' => 'Date',
                    'value' => function ($data) {
                        $date = Yii::$app->formatter->asDate($data->date);

                        return !empty($date) ? $date : null;
                    },
                ],
                [
                    'label' => 'Customer',
                    'value' => function ($data) {
                        return !empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
                    },
                ],
                [
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
                    'value' => function ($data) {
                        if ((int) $data->type === Invoice::TYPE_INVOICE) {
                            if ($data->status === 'Paid') {
                                return $data->total;
                            } else {
                                return $data->invoiceBalance;
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
                    'label' => 'Total',
                    'value' => function ($data) {
                        return $data->total;
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
    ]) ?>
    <div class="grid-row-open p-10">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
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
</div>
