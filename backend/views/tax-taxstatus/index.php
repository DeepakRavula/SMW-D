<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tax Taxstatuses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-taxstatus-index">


    <p>
        <?php echo Html::a('Create Tax Taxstatus', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'int',
            'tax_id',
            'tax_status_id',
            'exempt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
