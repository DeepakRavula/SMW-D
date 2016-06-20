<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TaxSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Taxes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-index">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
         
           	[
				'label' => 'Province Name',
				'value' => function($data) {
					$provinceName = ! (empty($data->province->name)) ? $data->province->name : null;
					return $provinceName;
                } 
			],
            'tax_rate',
            'since:date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    
    <p>
        <?php echo Html::a('Add', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

</div>
