<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\ItemCategory */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Item Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-category-index">


    <p>
        <?php echo Html::a('Create Item', ['item/create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            'code',
            [
                'label' => 'Item Category',
        'value' => function ($data) {
            return $data->itemCategory->name;
        },
            ],
            'description',
            'price',
            [
                'label' => 'Royalty Free',
        'value' => function ($data) {
            return $data->getRoyaltyFreeStatus();
        },
            ],
            [
                'label' => 'Tax',
        'value' => function ($data) {
            return $data->taxStatus->name;
        },
            ],
            [
                'label' => 'Status',
        'value' => function ($data) {
            return $data->getStatusType();
        },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        $url = Url::to(['item/update', 'id' => $model->id]);
                        return Html::a('<i class="glyphicon glyphicon-pencil"></i>', $url);
                    },
                    'delete' => function ($url, $model) {
                        $url = Url::to(['item/delete', 'id' => $model->id]);
                        return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url);
                    }
                ]
            ],
        ],
    ]); ?>

</div>