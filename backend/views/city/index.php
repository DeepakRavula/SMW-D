<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use common\components\gridView\AdminLteGridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\CitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cities';
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);

$addButton = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add'), ['create'], ['class' => 'btn btn-primary btn-sm']);
$this->params['action-button'] = $lastRole->name === User::ROLE_ADMINISTRATOR ? $addButton : null;
?>
<div class="grid-row-open p-10">
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['city/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'columns' => [
            [
                'attribute' => 'name',
                'label' => 'Name',
                'value' => function ($data) {
                    return !empty($data->name) ? $data->name : null;
                },
            ],
            [
                'attribute' => 'province_id',
                'label' => 'Province',
                'value' => function ($data) {
                    return !empty($data->province->name) ? $data->province->name : null;
                },
            ],
        ],
    ]); ?>

</div>
