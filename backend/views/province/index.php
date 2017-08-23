<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\components\gridView\AdminLteGridView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ProvinceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Provinces';
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);

$addButton = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add'), ['create'], ['class' => 'btn btn-primary btn-sm']);
$this->params['action-button'] = $lastRole->name === User::ROLE_ADMINISTRATOR ? $addButton : null;
?>
<div class="grid-row-open">
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['province/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        //'filterModel' => $searchModel,
        'columns' => [
            'name',
            'tax_rate',
            [
                'label' => 'Country Name',
                'value' => function ($data) {
                    return !empty($data->country->name) ? $data->country->name : null;
                },
            ],
        ],
    ]); ?>

</div>
