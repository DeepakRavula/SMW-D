<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use common\models\InvoiceLineItem;
use backend\assets\CustomGridAsset;
use common\models\ItemCategory;
use common\models\Invoice;
CustomGridAsset::register($this);
Yii::$app->assetManager->bundles['kartik\grid\GridGroupAsset'] = false;
 /*
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style>
  .table > tbody > tr.success > td ,.table > tbody > tr.kv-grid-group-row > td,.table > tbody > tr.kv-page-summary > td{
    background-color: white !important;
    pointer-events:none;
}
</style>
<script type='text/javascript' src="<?php echo Url::base(); ?>/js/kv-grid-group.js"></script>
<?php if ($searchModel->groupByMethod) : ?>
		<?php
       $columns = [
                [
                'value' => function ($data) {
                    if (!empty($data->invoice->date)) {
                        $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->invoice->date);
                        return $lessonDate->format('l, F jS, Y');
                    }

                    return null;
                },
                'contentOptions' => ['style' => 'font-weight:bold;font-style:italic;font-size:14px;text-align:left','class'=>'main-group'],

                'group' => true,
                'groupedRow' => true,
                'groupFooter'=>function ($model, $key, $index, $widget) { // Closure method
                return [
                    'mergeColumns'=>[[1]], // columns to merge in summary
                    'content'=>[              // content to show in each summary cell
                        1=> "Total for   ".\DateTime::createFromFormat('Y-m-d H:i:s', $model->invoice->date)->format('l, F jS, Y'),
                       2=>GridView::F_SUM,

                    ],
                    'contentFormats'=>[      // content reformatting for each summary cell

                        2=>['format'=>'number', 'decimals'=>2],

                    ],
                    'contentOptions'=>[      // content html attributes for each summary cell
                        1=>['style' => 'text-align:left;'],
                        2=>['style'=>'text-align:right'],

                    ],
                    // html attributes for group summary row
                    'options'=>['class'=>'success','style'=>'font-weight:bold;']
                ];
            },


            ],
                [
                'label' => 'Item Category',
                'value' => function ($data) {
                    return $data->itemCategory->name;
                },
                'contentOptions' => ['style' => 'font-weight:bold;font-style:italic;font-size:14px;text-align:left','class'=>'main-group'],
        ],
 
                        [
                'label' => 'Amount',
                'format' => ['decimal', 2],
                'value' => function ($data) use ($searchModel) {
                    $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
                    $amount = 0;
                  $payments = InvoiceLineItem::find()
                                 ->notDeleted()
                                 ->joinWith(['invoice' => function ($query) use ($locationId) {
                                 $query->notDeleted()
                                ->notCanceled()
                                ->notReturned()
                                ->andWhere(['invoice.type' => Invoice::TYPE_INVOICE])
                                ->location($locationId);
                     }])
                      ->joinWith('itemCategory')
                         ->andWhere([
                            'item_category.id' => $data->itemCategory->id,
                            'DATE(invoice.date)' => (new \DateTime($data->invoice->date))->format('Y-m-d')
                        ])
                        ->all();
                    foreach ($payments as $payment) {
                        $amount += $payment->itemTotal;
                    }

                    return Yii::$app->formatter->asDecimal($amount,2);
                },
                'contentOptions' => ['class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ]
        ];
        ?>
	<?php else : ?>
    <?php $columns = [
                [
                'value' => function ($data) {
                    if (!empty($data->invoice->date)) {
                        $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->invoice->date);
                        return $lessonDate->format('l, F jS, Y');
                    }

                    return null;
                },
                'contentOptions' => ['style' => 'font-weight:bold;font-style:italic;font-size:14px;text-align:left','class'=>'main-group'],
                
                'group' => true,
                'groupedRow' => true,
               'groupFooter'=>function ($model, $key, $index, $widget) { // Closure method
                return [
                    'mergeColumns'=>[[2, 4]], // columns to merge in summary
                    'content'=>[  
                        // content to show in each summary cell
                       2=> "Total for   ".\DateTime::createFromFormat('Y-m-d H:i:s', $model->invoice->date)->format('l, F jS, Y'),
                       5=>GridView::F_SUM,

                    ],
                    'contentFormats'=>[      // content reformatting for each summary cell
                        5=>['format'=>'number', 'decimals'=>2],

                    ],
                    'contentOptions'=>[      // content html attributes for each summary cell
                        2=>['style' => 'text-align:left;font-style:italic'],
                        5=>['style'=>'text-align:right'],

                    ],
                    // html attributes for group summary row
                    'options'=>['class'=>'success','style'=>'font-weight:bold;']
                ];
            },
                
                
            ],
                [
                'label' => 'Item Category',
                'value' => function ($data) {
                    return $data->itemCategory->name;
                },
                'contentOptions' => ['style' => 'font-weight:bold;font-style:italic;font-size:14px;text-align:left','class'=>'main-group'],
                'group' => true,
                'groupedRow' => true,
                'subGroupOf' => 0,
            'groupFooter'=>function ($model, $key, $index, $widget) { // Closure method
                return [
                    'mergeColumns'=>[[2, 4]],// columns to merge in summary
                    'content'=>[              // content to show in each summary cell
                       2=> "Total for   ".$model->itemCategory->name,
                       5=>GridView::F_SUM,
                       
                    ],
                    'contentFormats'=>[      // content reformatting for each summary cell
                        
                        5=>['format'=>'number', 'decimals'=>2],
                       
                    ],
                    'contentOptions'=>[
                        2=>['style'=>'font-style:italic'],// content html attributes for each summary cell
                        5=>['style'=>'text-align:right'],
                        
                    ],
                    // html attributes for group summary row
                    'options'=>['class'=>'success','style'=>'font-weight:bold;']
                ];
            },
        ],
                 [
                     'label'=>'ID',
                'value' => function ($data) {
                    return $data->invoice->getInvoiceNumber();
                },
                    
            ],
                                   [
                     'label'=>'Customer',
                'value' => function ($data) {
                         return !empty($data->invoice->user->publicIdentity) ? $data->invoice->user->publicIdentity : null;
                },

            ],

                [
                'label' => 'Description',
                'value' => function ($data) {
                    return $data->description;
                },
                    
                    'pageSummary' => 'Grand Total',
                    'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left'],
            ],

                [
                'label' => 'Amount',
                'format' => ['decimal', 2],
                'value' => function ($data) {
                    return Yii::$app->formatter->asDecimal($data->itemTotal);
                },
                'contentOptions' => ['class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
        ];
        ?>
<?php endif; ?>
<div class="grid-row-open">
	<?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'options' =>['class' => 'payment-table'],
        'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel) {
        $url = Url::to(['invoice/view', 'id' => $model->invoice->id]);
        $data = ['data-url' => $url];
        return $data;
    },
                'summary' => false,
                'emptyText' => false,
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
</div>
