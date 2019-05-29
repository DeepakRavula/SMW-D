<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Invoice;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use backend\models\search\UserSearch;
use yii\helpers\ArrayHelper;
use common\models\Student;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\User;

?>
<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'title' => 'Unused Credits',
        'withBorder' => true,
    ])
    ?>

<div class="clearfix"></div>
<?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
    'id' => 'customer-credits-grid'
]) ?>
<?php echo  GridView::widget([
    'dataProvider' => $creditsDataProvider,
    'options' => ['class' => 'col-md-12', 'id' => 'account-receivable-report-credits-available'],
    'summary' => false,
    'showPageSummary' => true,
    'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered table table-condensed'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [      
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'label' => 'ID',
            'value' => 'reference',
       ],
 [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'label' => 'Date',
        'value' => 'date',
    ],

  
    [
        'format' => 'currency',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right credit-value'],
        'label' => 'Amount',
        'value' => 'amount',
        'hAlign' => 'right',
        'pageSummary' => true,
        'pageSummaryFunc' => GridView::F_SUM,
    ],
    ]
]); ?>
<?php \yii\widgets\Pjax::end(); ?>

<?php LteBox::end() ?>
	
