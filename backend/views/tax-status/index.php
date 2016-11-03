<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TaxStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tax Statuses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-status-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <p>
        <?php echo Html::a('Create Tax Status', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
