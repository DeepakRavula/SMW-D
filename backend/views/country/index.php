<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\CountrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Countries';
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add'), ['create'], ['class' => 'btn btn-primary btn-sm']);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grid-row-open">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['country/view', 'id' => $model->id]);
        return ['data-url' => $url];
        },
        //'filterModel' => $searchModel,
        'columns' => [
            'name',

        ],
    ]); ?>

</div>
