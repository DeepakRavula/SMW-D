<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TaxCodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tax Codes';
$this->params['subtitle'] = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add new'), ['create'],['class' => 'btn btn-primary btn-sm']);
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
$this->registerJs("
    $('td').click(function (e) {
        var id = $(this).closest('tr').data('id');
        if(e.target == this)
            location.href = '" . Url::to(['tax-code/view']) . "?id=' + id;
    });

");
?>
<div class="tax-code-index">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
		'rowOptions'   => function ($model, $key, $index, $grid) {
        	return ['data-id' => $model->id];
    	},
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
			[
				'label' => 'Tax Name',
				'attribute' => 'tax_type_id',
				'value' => function($data){
					return $data->taxType->name;
				}
			],
			[
				'attribute' => 'province_id',
				'value' => function($data){
					return $data->province->name;
				}
			],
            'rate',
            'start_date:date',
        ],
    ]); ?>

</div>
