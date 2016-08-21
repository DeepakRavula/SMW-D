<?php

use yii\grid\GridView;
use common\models\Invoice;
?>
<style>
    hr{
        margin: 10px 0;
    }
</style>
<div class="col-md-12">
    <h4 class="pull-left m-r-20">Invoices</h4>
    <a href="#" class="add-new-invoice text-add-new"><i class="fa fa-plus-circle"></i></a>
</div>
<div class="p-10">
    <?php echo $this->render('_misc-line-item') ?>
    <div class="dn invoice-create section-tab">
        <?php echo $this->render('_uninvoiced_lessons', [
            'unInvoicedLessonsDataProvider' => $unInvoicedLessonsDataProvider,
            'model' => new Invoice(),
            'searchModel' => $searchModel,
            'userModel' => $userModel
        ]) ?>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
</div>
<hr class="hr-ad-in right-side-faded">
<div id="add-misc-item" class="col-md-12">
	<h4 class="pull-left m-r-20">Add Misc</h4>
	<a href="#" class="add-new-misc text-add-new"><i class="fa fa-plus-circle"></i></a>
    <div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<hr class="hr-ad right-side-faded">
<?php yii\widgets\Pjax::begin() ?>
<?php echo GridView::widget([
        'dataProvider' => $invoiceDataProvider,
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
            'total',
        ],
    ]); ?>
<?php \yii\widgets\Pjax::end(); ?>

<script>
    $('.add-new-invoice').click(function(){
        $(this).hide();
        //$('.hr-ad-in').hide();
    });
</script>
