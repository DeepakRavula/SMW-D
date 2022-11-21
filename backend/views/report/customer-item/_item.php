<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use backend\assets\CustomGridAsset;

CustomGridAsset::register($this);
Yii::$app->assetManager->bundles['kartik\grid\GridGroupAsset'] = false;
 /*
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script type='text/javascript' src="<?php echo Url::base(); ?>/js/kv-grid-group.js"></script>
	<?php $columns = [
                [
                'value' => function ($data) {
                    return $data->itemCategory->name;
                },
                'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left','class'=>'main-group'],
                'group' => true,
                'groupedRow' => true,
                'groupFooter' => function ($model, $key, $index, $widget) {
                    return [
                        'mergeColumns' => [[1]],
                        'content' => [
                            2 => GridView::F_SUM,
                        ],
                        'contentFormats' => [
                            2 => ['format' => 'number', 'decimals' => 2],
                        ],
                        'contentOptions' => [
                            2 => ['style' => 'text-align:right'],
                        ],
                        'options' => ['style' => 'font-weight:bold;']
                    ];
                }
            ],
                [
                'label' => 'Description',
                'value' => function ($data) {
                    return $data->description;
                },
            ],
                [
                'label' => 'Price',
                'value' => function ($data) {
                    return $data->netPrice;
                },
                'contentOptions' => ['class' => 'text-right'],
                'hAlign' => 'right',
                'format' => ['decimal', 2],
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
        ];
        ?>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'emptyText' => false,
            'options' => ['class' => ''],
            'showPageSummary' => true,
            'headerRowOptions' => ['class' => 'bg-light-gray'],
            'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed', 'id' => 'payment'],
            'pjax' => true,
            'pjaxSettings' => [
                'neverTimeout' => true,
                'options' => [
                    'id' => 'item-listing',
                ],
            ],
            'columns' => $columns,
        ]);
        ?>
