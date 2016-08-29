<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Invoice;
use yii\bootstrap\Tabs;
?>
<style>
    hr{
        margin: 10px 0;
    }
</style>
<div class="col-md-12">
    <h4 class="pull-left m-r-20">Pro Forma Invoices</h4>
    <?php
        echo Html::a(
            Html::tag('i', '', ['class' => 'fa fa-plus-circle']),
            Url::to(['/invoice/create','InvoiceSearch[type]' => INVOICE::TYPE_INVOICE]), [
            'class' => 'add-new-invoice text-add-new',
            ]);
    ?>
</div>
<?php yii\widgets\Pjax::begin() ?>
<?php echo GridView::widget([
    'dataProvider' => $proFormaInvoiceDataProvider,
    'options' => ['class' => 'col-md-12'],
    'tableOptions' =>['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray' ],
    'rowOptions' => function ($model, $key, $index, $grid) {
        $u= \yii\helpers\StringHelper::basename(get_class($model));
        $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
        return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
    },
    'columns' => [
        [
            'label' => 'Student Name',
            'value' => function($data) {
                return ! empty($data->lineItems[0]->lesson->enrolment->student->fullName) ? $data->lineItems[0]->lesson->enrolment->student->fullName. ' (' .$data->lineItems[0]->lesson->enrolment->program->name. ')' : null;
            },
        ],
        [
        'label' => 'Date',
            'value' => function($data) {
                return ! empty($data->date) ? Yii::$app->formatter->asDate($data->date) : null;
            }
        ],
        [
            'label' => 'Status',
            'value' => function($data) {
                return $data->getStatus(); 
            },
        ],
		[ 
			'attribute' => 'total',
			'label' => 'Total',
			'headerOptions' => ['class' => 'text-right'],
			'contentOptions' => ['class' => 'text-right'],
			'enableSorting' => false,
		],
    ],
]); ?>
<?php \yii\widgets\Pjax::end(); ?>
<script>
    $('.add-new-invoice').click(function(){
        $(this).hide();
        //$('.hr-ad-in').hide();
    });
</script>
