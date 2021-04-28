<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\html;
use common\models\InvoiceLineItem;
use backend\assets\CustomGridAsset;
use common\components\gridView\KartikGridView;
use common\models\ItemCategory;
use common\models\Invoice;
use common\models\Location;

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
  .table > tbody > tr.success > td ,.table > tbody > tr.kv-grid-group-row > td{
	background-color: white !important;
}
.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: white !important;
}
.table > thead:first-child > tr:first-child > th{
    color : black;
    background-color : lightgray;
}
.table > tbody >tr.warning >td {
    font-size:17px;
}
.kv-page-summary {
    border-top:none;
    font-weight: bold;
}
.table > tbody + tbody {
     border-top: none;
}
</style>
<script type='text/javascript' src="<?php echo Url::base(); ?>/js/kv-grid-group.js"></script>
<?php $totalReportValue = ItemCategory::getTotal($dataProvider->query->all()); ?>
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
            'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left', 'class' => 'main-group'],

            'group' => true,
            'groupedRow' => true,
            'groupFooter' => function ($model, $key, $index, $widget) { // Closure method
                return [
                    'mergeColumns' => [[1]], // columns to merge in summary
                    'content' => [              // content to show in each summary cell
                        4 => GridView::F_SUM,

                    ],
                    'contentFormats' => [      // content reformatting for each summary cell

                        4 => ['format' => 'number', 'decimals' => 2],

                    ],
                    'contentOptions' => [      // content html attributes for each summary cell
                        1 => ['style' => 'text-align:left;'],
                        4 => ['style' => 'text-align:right;font-weight:bold '],

                    ],
                    // html attributes for group summary row
                    'options' => ['class' => 'success']
                ];
            },


        ],
        [
            'label' => 'Item Category',
            'value' => function ($data) {
                return $data->itemCategory->name;
            },
            'pageSummary' => 'Page Total',
            'contentOptions' => ['style' => 'font-weight:bold;font-style:italic;font-size:14px;text-align:left', 'class' => 'main-group'],
            'pageSummary' => 'Page Total',
            'contentOptions' => ['style' => 'font-size:14px;text-align:left'],
        ],
        [
            'label' => 'Subtotal',
            'format' => ['decimal', 2],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->netPrice);
            },
            'contentOptions' => ['class' => 'text-right dollar'],
            'hAlign' => 'right',
        ],

        [
            'label' => 'Tax',
            'format' => ['decimal', 2],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal(round($data->tax_rate, 2));
            },
            'contentOptions' => ['class' => 'text-right dollar'],
            'hAlign' => 'right',
        ],

        [
            'label' => 'Total',
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

                return Yii::$app->formatter->asCurrency($amount);
            },
            'contentOptions' => ['class' => 'text-right'],
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM,
            'pageSummaryOptions' => ['class' => 'dollar'],
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
            'contentOptions' => ['style' => 'font-style:italic;font-size:14px;text-align:left', 'class' => 'main-group'],

            'group' => true,
            'groupedRow' => true,
            'groupFooter' => function ($model, $key, $index, $widget) { // Closure method
                $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
                $subTotal = 0;
                $taxRate = 0;
                $itemTotal = 0;
                $invoiceLineItems = InvoiceLineItem::find()
                ->notDeleted()
                ->joinWith(['invoice' => function ($query) use ($locationId, $model) {
                        $query->notDeleted()
                            ->notCanceled()
                            ->notReturned()
                            ->andWhere(['invoice.type' => Invoice::TYPE_INVOICE])
                            ->location($locationId)
                            ->between((new \DateTime($model->invoice->date))->format('Y-m-d'), (new \DateTime($model->invoice->date))->format('Y-m-d'));
            }])
                ->all();
                foreach ($invoiceLineItems as $invoiceLineItem) {
                    $subTotal += $invoiceLineItem->netPrice;
                    $taxRate  += $invoiceLineItem->tax_rate;
                    $itemTotal += $invoiceLineItem->itemTotal;
                }
                return [
                    'mergeColumns' => [[2,4]], // columns to merge in summary
                    'content' => [  
                        // content to show in each summary cell
                        5 => Yii::$app->formatter->asCurrency(round($subTotal, 2)),
                        6 => Yii::$app->formatter->asCurrency(round($taxRate, 2) == 0 ? '0.00' : round($taxRate, 2)),
                        7 => Yii::$app->formatter->asCurrency(round($itemTotal, 2)),
                    ],
                    'contentOptions' => [      // content html attributes for each summary cell
                        2 => ['style' => 'text-align:left;font-style:italic'],
                        5 => ['style' => 'text-align:right;font-style:italic'],
                        6 => ['style' => 'text-align:right;font-style:italic'],
                        7 => ['style' => 'text-align:right;font-style:italic'],
                    ],
                    // html attributes for group summary row
                    'options' => ['class' => 'success']
                ];
            },
        ],
        [
            'label' => 'Item Category',
            'value' => function ($data) {
                return $data->itemCategory->name;
            },
            'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left', 'class' => 'main-group'],
            'group' => true,
            'groupedRow' => true,
            'subGroupOf' => 0,
            'groupFooter' => function ($model, $key, $index, $widget) { // Closure method
                return [
                    'mergeColumns' => [[2, 4]],// columns to merge in summary
                    'content' => [    
                        5 => GridView::F_SUM,
                        6 => GridView::F_SUM,         // content to show in each summary cell
                        7 => GridView::F_SUM,

                    ],
                    'contentFormats' => [      // content reformatting for each summary cell
                        5 => ['format' => 'number', 'decimals' => 2, 'thousandSep'=>','],
                        6 => ['format' => 'number', 'decimals' => 2, 'thousandSep'=>','],
                        7 => ['format' => 'number', 'decimals' => 2, 'thousandSep'=>','],

                    ],
                    'contentOptions' => [
                        5 => ['style' => 'text-align:right', 'class' => 'dollar'],
                        6 => ['style' => 'text-align:right', 'class' => 'dollar'],
                        7 => ['style' => 'text-align:right', 'class' => 'dollar'],

                    ],
                    // html attributes for group summary row
                    'options' => ['class' => 'success', 'style' => 'font-weight:bold;']
                ];
            },
        ],
        [
            'label' => 'ID',
            'value' => function ($data) {
                return $data->invoice->getInvoiceNumber();
            },
            'contentOptions' => ['style' => 'width: 8%;font-size:14px;text-align:left'],

        ],
        [
            'label' => 'Customer',
            'value' => function ($data) {
                return !empty($data->invoice->user->publicIdentity) ? $data->invoice->user->publicIdentity : null;
            },
            'contentOptions' => ['style' => 'width: 25%'],
        ],

        [
            'label' => 'Description',
            'value' => function ($data) {
                return $data->description;
            },


            'contentOptions' => ['style' => 'font-size:14px;text-align:left'],
        ],

        [
            'label' => 'Subtotal',
           
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->netPrice);
            },
            'contentOptions' => ['class' => 'text-right dollar', 'style' => 'width: 4%'],
            'hAlign' => 'right',
        ],

        [
            'label' => 'Tax',
           
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal(round($data->tax_rate, 2));
            },
            'contentOptions' => ['class' => 'text-right dollar', 'style' => 'width: 4%'],
            'hAlign' => 'right',
        ],

        [
            'label' => 'Total',
           
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->itemTotal);
            },
            'contentOptions' => ['class' => 'text-right dollar', 'style => width: 4%'],
            'hAlign' => 'right',
        ],
    ];
    ?>
<?php endif; ?>
<?php
  $title = empty($this->caption) ? Yii::t('kvgrid', 'Grid Export') : $this->caption;
  $pdfHeader = [
      'L' => [
          'content' => Yii::t('kvgrid', 'Yii2 Grid Export (PDF)'),
          'font-size' => 8,
          'color' => '#333333',
      ],
      'C' => [
          'content' => $title,
          'font-size' => 16,
          'color' => '#333333',
      ],
      'R' => [
          'content' => Yii::t('kvgrid', 'Generated') . ': ' . date('D, d-M-Y'),
          'font-size' => 8,
          'color' => '#333333',
      ],
  ];
  $pdfFooter = [
      'L' => [
          'content' => Yii::t('kvgrid', '© Krajee Yii2 Extensions'),
          'font-size' => 8,
          'font-style' => 'B',
          'color' => '#999999',
      ],
      'R' => [
          'content' => '[ {PAGENO} ]',
          'font-size' => 10,
          'font-style' => 'B',
          'font-family' => 'serif',
          'color' => '#333333',
      ],
      'line' => true,
  ];
?>
<div class="grid-row-open">
	<?= KartikGridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'payment-table'],
    'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel) {
        
        $url = Url::to(['invoice/view', 'id' => $model->invoice->id]);
        $data = ['data-url' => $url];
        if ($searchModel->groupByMethod) {
            $data = array_merge($data, ['class' => 'click-disable']);
    }
        return $data;
    },
    'summary' => false,
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed table-itemcategory-report', 'id' => 'payment'],
    'pjax' => true,
    'pjaxSettings' => [
        'neverTimeout' => true,
        'options' => [
            'id' => 'item-listing',
        ],
    ],
    'columns' => $columns,
    'toolbar' => [
        ['content' => $this->render('_button', [
            'model' => $searchModel
            ])],
        '{export}',
        '{toggleData}',
        ['content' => Html::a('<i class="fa fa-print btn-default btn-lg"></i>', '#', ['id' => 'print'])],
    ],
    'panel' => [
        'type' => GridView::TYPE_DEFAULT,
        'heading' => 'Items Sold by Category'
    ],
    'exportConfig'=> [
        GridView::HTML=>[
            'label' => 'HTML',
           
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            
          
            'mime' => 'application/html',
           
        ],
        GridView::CSV=>[
            'label' => 'CSV',
           
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            
          
            'mime' => 'application/csv',
           
        ],
        GridView::TEXT=>[
            'label' => 'Text',
           
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            
          
            'mime' => 'application/text',
           
        ],
        GridView::EXCEL=>[
            'label' => 'Excel',
           
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            
          
            'mime' => 'application/excel',
           
        ],
        GridView::PDF => [
            'label' => Yii::t('kvgrid', 'PDF'),
            'icon' => 'floppy-disk',
            'iconOptions' => ['class' => 'text-danger'],
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            'filename' => Yii::t('kvgrid', 'grid-export'),
            'alertMsg' => Yii::t('kvgrid', 'The PDF export file will be generated for download.'),
            'options' => ['title' => Yii::t('kvgrid', 'Portable Document Format')],
            'mime' => 'application/pdf',
            'config' => [
                'mode' => 'UTF-8',
                'format' => 'A4-L',
                'destination' => 'D',
                'marginTop' => 20,
                'marginBottom' => 20,
                'cssInline' => '.kv-wrap{padding:20px;}' .
                    '.kv-align-center{text-align:center;}' .
                    '.kv-align-left{text-align:left;}' .
                    '.kv-align-right{text-align:right;}' .
                    '.kv-align-top{vertical-align:top!important;}' .
                    '.kv-align-bottom{vertical-align:bottom!important;}' .
                    '.kv-align-middle{vertical-align:middle!important;}' .
                    '.kv-page-summary{border-top:4px double #ddd;font-weight: bold;}' .
                    '.kv-table-footer{border-top:4px double #ddd;font-weight: bold;}' .
                    '.kv-table-caption{font-size:1.5em;padding:8px;border:1px solid #ddd;border-bottom:none;}',
                'methods' => [
                    'SetHeader' => [
                        ['odd' => $pdfHeader, 'even' => $pdfHeader],
                    ],
                    'SetFooter' => [
                        ['odd' => $pdfFooter, 'even' => $pdfFooter],
                    ],
                ],
                'options' => [
                    'title' => $title,
                    'subject' => Yii::t('kvgrid', 'PDF export generated by kartik-v/yii2-grid extension'),
                    'keywords' => Yii::t('kvgrid', 'krajee, grid, export, yii2-grid, pdf'),
                ],
                'contentBefore' => '',
                'contentAfter' => '',
            ],
        ],
        GridView::JSON=>[
            'label' => 'JSON',
           
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            
          
            'mime' => 'application/json',
           
        ],
      
    ],
]);
?></div>

<script>
var recordCount = '<?= ItemCategory::getTotalCount($dataProvider->query->all()); ?>';
$(document).ready(function() {
    if (recordCount > 0 && recordCount <= 20) {
        report.addNewRow();
    }
});

$(document).on('pjax:success', function() {
    if (recordCount >= 20) {
        var activePage = $('ul.pagination li.active > a').text();
        var lastPage = parseInt(recordCount / 20);
        var remainder = recordCount % 20;
        if (remainder >= 1) {
            lastPage = lastPage + 1;
        }
        if (lastPage == activePage) {
            report.addNewRow();
        }
    }
});

var report = {
        addNewRow: function () {
    var newSummaryContainer=$("<tbody>");
    var newRow = $("<tr class='report-footer-grandtotal click-disable'>");
        var cols = "";
        var totalReportValue=<?= Yii::$app->formatter->asDecimal($totalReportValue, 2) ?>;
        var groupByMethod = $("#group-by-method").is(":checked");
        colSpanValue=6;
        if(groupByMethod) {
        colSpanValue=1;
        }
        cols += '<td colspan='+colSpanValue+' class="text-right">'+totalReportValue+'</td>';   
        newRow.append(cols);
        newSummaryContainer.append(newRow);
        $("table.table-itemcategory-report").append(newSummaryContainer);

}
};

  </script>
