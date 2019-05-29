<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
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
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
    'id' => 'customer-credits-grid'
]) ?>
<?php echo  GridView::widget([
    'dataProvider' => $creditsDataProvider,
    'options' => ['class' => 'col-md-12'],
    'summary' => false,
    'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered table table-condensed'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [      
 [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => function ($model) {
            return [
                'creditId' => $model['id'],
                'class' => 'text-left credit-type'
            ];
        },
        'label' => 'Type',
        'value' => 'type',
    ],

   [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'label' => 'Reference',
        'value' => 'reference',
   ],


    [
        'format' => 'currency',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right credit-value'],
        'label' => 'Amount',
        'value' => 'amount'
    ],
    ]
]); ?>
<?php \yii\widgets\Pjax::end(); ?>
</div>
<?php LteBox::end() ?>
	
