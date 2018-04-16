<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use common\models\InvoiceLineItem;
use backend\assets\CustomGridAsset;
use common\models\ItemCategory;
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
                'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left','class'=>'main-group'],

                'group' => true,
                'groupedRow' => true,
                            'groupFooter'=>function ($model, $key, $index, $widget) { // Closure method
                return [
                    'mergeColumns'=>[[2, 3]], // columns to merge in summary
                    'content'=>[              // content to show in each summary cell
                      
                       4=>GridView::F_SUM,

                    ],
                    'contentFormats'=>[      // content reformatting for each summary cell

                        4=>['format'=>'number', 'decimals'=>2],

                    ],
                    'contentOptions'=>[      // content html attributes for each summary cell
                        2=>['style' => 'text-align:left'],
                        4=>['style'=>'text-align:right'],

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
                'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left','class'=>'main-group'],
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
                'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left','class'=>'main-group'],
                
                'group' => true,
                'groupedRow' => true,
               'groupFooter'=>function ($model, $key, $index, $widget) { // Closure method
                return [
                    'mergeColumns'=>[[2, 3]], // columns to merge in summary
                    'content'=>[              // content to show in each summary cell
                       3=>"xxxxxxx",
                       4=>GridView::F_SUM,

                    ],
                    'contentFormats'=>[      // content reformatting for each summary cell
                        2=>['format'=>'string'],
                        4=>['format'=>'number', 'decimals'=>2],

                    ],
                    'contentOptions'=>[      // content html attributes for each summary cell
                        2=>['style' => 'text-align:left'],
                        4=>['style'=>'text-align:right'],

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
                'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left','class'=>'main-group'],
                'group' => true,
                'groupedRow' => true,
                'subGroupOf' => 0,
            'groupFooter'=>function ($model, $key, $index, $widget) { // Closure method
                return [
                    'mergeColumns'=>[[2, 3]],// columns to merge in summary
                    'content'=>[              // content to show in each summary cell
                       0=> $model->itemCategory->name,
                       4=>GridView::F_SUM,
                       
                    ],
                    'contentFormats'=>[      // content reformatting for each summary cell
                        
                        4=>['format'=>'number', 'decimals'=>2],
                       
                    ],
                    'contentOptions'=>[
                        2=>['style'=>'font-variant:small-caps'],// content html attributes for each summary cell
                       // 2=>['style' => 'text-align:left'],
                        4=>['style'=>'text-align:right'],
                        
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
	<?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => ''],
                'summary' => false,
                'emptyText' => false,
        'showPageSummary' => true,
                'headerRowOptions' => ['class' => 'bg-light-gray'],
                'rowOptions'=>['class' => 'item-category-report-invoice-click'],
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
<script>
    $(document).ready(function(){
 $(document).on('click', '.item-category-report-invoice-click', function(){
     var invoiceLineItemId=$(this).attr('data-key');
     var params = $.param({'lineItemId' : invoiceLineItemId});
     		$.ajax({
                    url    :'<?= Url::to(['item-category/invoice-number']); ?>?&' + params,
                    type   : 'get',
                    dataType: 'json',
                    success: function(response)
                    {
                       if(response) {
                        var url = '<?= Url::to(['invoice/view']); ?>?id='+response;
                        window.location.href=url;
			   }
				}
			});
    });
    });
    </script>