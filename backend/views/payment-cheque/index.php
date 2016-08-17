<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\PaymentCheque */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Cheques';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-cheque-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Create Payment Cheque', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'payment_id',
            'number',
            'date',
            'bank_name',
            // 'bank_branch_name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
