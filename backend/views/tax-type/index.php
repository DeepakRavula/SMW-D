<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TaxSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Taxes';
$this->params['action-button'] = Html::a('<i class="fa fa-plus" aria-hidden="true"></i>', ['create'], ['class' => 'btn btn-primary']);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-index">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $u = \yii\helpers\StringHelper::basename(get_class($model));
            $u = yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);

            return ['id' => $model['id'], 'style' => 'cursor: pointer', 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'columns' => [

               [
                'label' => 'Province Name',
                'value' => function ($data) {
                    $provinceName = !(empty($data->province->name)) ? $data->province->name : null;

                    return $provinceName;
                },
            ],
            'tax_rate',
            'since:date',

        ],
    ]); ?>
    
</div>
