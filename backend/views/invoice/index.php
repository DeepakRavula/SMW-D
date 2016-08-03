<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Invoice;
use backend\models\search\InvoiceSearch;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\models\search\InvoiceSearch */

$this->title = (int) $searchModel->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? 'Pro-forma Invoice' : 'Invoice';
$this->params['subtitle'] = Html::a('<i class="fa fa-plus" aria-hidden="true"></i>', ['invoice/create', 'Invoice[type]' => $searchModel->type], ['class' => 'btn btn-success']); 
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-index p-10">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
	<?php $columns = [
			'invoice_number',
            [
			'label' => 'Date',
				'value' => function($data) {
					$date = Yii::$app->formatter->asDate($data->date); 
					return ! empty($date) ? $date : null;
                },
			],
			[
			    'label' => 'Customer',
                'value' => function($data) {
                    return ! empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
                },
            ],
	    	[
				'label' => 'Status',
				'value' => function($data) {
					return $data->status;
                },
			],
            'total',
        ];

		if((int) $searchModel->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE) {
			array_shift($columns);			
		}
		?>
	<?php yii\widgets\Pjax::begin() ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'columns' => $columns,
    ]); ?>
	<?php \yii\widgets\Pjax::end(); ?>

</div>
