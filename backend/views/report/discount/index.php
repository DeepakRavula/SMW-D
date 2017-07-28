<?php
use kartik\grid\GridView;
use yii\helpers\Url;
use backend\assets\CustomGridAsset;
CustomGridAsset::register($this);
Yii::$app->assetManager->bundles['kartik\grid\GridGroupAsset'] = false;

$this->title = 'Discount Report';
 /*
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="payments-index p-10">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
<script type='text/javascript' src="<?php echo Url::base(); ?>/js/kv-grid-group.js"></script>
<style type="text/css" src="/admin/css/group-grid.css"></style>
<div class="payments-index">
    <?php
        $columns = [
            [
                'value' => function ($data) {
                    if (!empty($data->invoice->date)) {
                        $invoiceDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->invoice->date);
                        return $invoiceDate->format('Y-m-d');
                    }

                    return null;
                },
                'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left','class'=>'main-group'],
                'group' => true,
                'groupedRow' => true,
                'groupFooter' => function ($model, $key, $index, $widget) {
                    return [
                        'mergeColumns' => [[2, 3]],
                        'content' => [
                            5 => GridView::F_SUM,
                        ],
                        'contentFormats' => [
                            5 => ['format' => 'number', 'decimals' => 2],
                        ],
                        'contentOptions' => [
                            5 => ['style' => 'text-align:right'],
                        ],
                        'options' => ['style' => 'font-weight:bold;font-size:14px;']
                    ];
                }
            ],
            [
                'label' => 'Customer',
                'value' => function ($data) {
                    return !empty($data->invoice->user->publicIdentity) ? $data->invoice->user->publicIdentity : null;
                },
                'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left'],
                'group' => true,
                'groupedRow' => true,
                'subGroupOf' => 0,
                'groupFooter' => function ($model, $key, $index, $widget) {
                    return [
                        'mergeColumns' => [[2, 4]],
                        'content' => [
                            5 => GridView::F_SUM,
                        ],
                        'contentFormats' => [
                            5 => ['format' => 'number', 'decimals' => 2],
                        ],
                        'contentOptions' => [
                            5 => ['style' => 'text-align:right'],
                        ],
                        'options' => ['class' => 'success', 'style' => 'font-weight:bold;font-size:14px']
                    ];
                },
            ],
            [
                'label' => 'Code',
                'value' => function($data) {
                    return $data->code;
                },
                'contentOptions' => ['style' => 'font-size:14px'],
            ],
            [
                'label' => 'Description',
                'value' => function ($data) {
                    return $data->description;
                },
                'contentOptions' => ['style' => 'font-size:14px'],
            ],
            [
                'label' => 'Qty',
                'contentOptions' => ['style' => 'font-size:14px'],
                'value' => function ($data) {
                    return $data->unit;
                },
            ],
            [
                'label' => 'Discount',
                'value' => function ($data) {
                    return $data->discount;
                },
                'contentOptions' => ['class' => 'text-right', 'style' => 'font-size:14px'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Price',
                'value' => function ($data) {
                    return $data->amount;
                },
            ],
        ];
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => ''],
        'showPageSummary' => true,
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed', 'id' => 'payment'],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'discount-report',
            ],
        ],
        'columns' => $columns,
    ]); ?>
</div>