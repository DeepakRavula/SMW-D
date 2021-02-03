<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use backend\assets\CustomGridAsset;
use common\components\gridView\KartikGridView;
use yii\helpers\Html;

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
                    if (!empty($data->invoice->date)) {
                        $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->invoice->date);
                        return $lessonDate->format('l, F jS, Y');
                    }

                    return null;
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
                            2 => ['format' => 'number', 'decimals' => 2,  'thousandSep'=>','],
                        ],
                        'contentOptions' => [
                            2 => ['style' => 'text-align:right', 'class' => 'dollar'],
                        ],
                        'options' => ['style' => 'font-weight:bold;']
                    ];
                }
            ],
                [
                'label' => 'Item',
                'value' => function ($data) {
                    return $data->item->code;
                },
            ],
                [
                'label' => 'Amount',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDecimal($data->itemTotal);
                },
                'format' => ['decimal', 2],
                'contentOptions' => ['class' => 'text-right dollar'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM,
                'pageSummaryOptions' => ['class' => 'dollar'],
            ],
        ];
        ?>
        <?= KartikGridView::widget([
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
            'toolbar' => [
                ['content' => Html::a('<i class="fa fa-print btn-default btn-lg"></i>', '#', ['id' => 'print'])],
            ],
            'panel' => [
                'type' => GridView::TYPE_DEFAULT,
                'heading' => 'Items'
            ],
        ]);
        ?>
