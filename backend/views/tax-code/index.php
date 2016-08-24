<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TaxCodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tax Codes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-code-index">

    <p>
        <?php echo Html::a('Create Tax Code', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
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

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
