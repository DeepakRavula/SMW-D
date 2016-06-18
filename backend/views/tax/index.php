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

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Create Tax', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'province_id',
            'tax_rate',
            'since',
            
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
