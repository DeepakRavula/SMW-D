<?php

use yii\helpers\Html;
use common\components\gridView\KartikGridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\PrivateLessonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Proforma Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="proforma-invoice-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]);?>
<div class="grid-row-open">
    <?php echo KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['proforma-invoice/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            [
                'attribute' => 'number',
                'label' => 'Number',
                'contentOptions' => ['style' => 'width:100px'],
                'value' => function ($data) {
                    return $data->getProformaInvoiceNumber();
                },
            ],

            [
                'attribute' => 'customer',
                'label' => 'Customer',
                'value' => function ($data) {
                    return !empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
                }
    

            ],
            [
                'attribute' => 'phone',
                'label' => 'Phone',
                'value' => function ($data) {
                    return !empty($data->user->phoneNumber->number) ? $data->user->phoneNumber->number : null;
                },
            ],
            [
                'attribute' => 'phone',
                'label' => 'Total',
                'value' => function ($data) {
                    return !empty($data->total) ? $data->total : null;
                },
            ],
        ],
    ]); ?>
</div>
</div>