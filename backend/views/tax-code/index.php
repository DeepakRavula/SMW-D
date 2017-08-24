<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\gridView\AdminLteGridView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TaxCodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tax Codes';
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);

$addButton = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add'), ['create'], ['class' => 'btn btn-primary btn-sm']);
$this->params['action-button'] = $lastRole->name === User::ROLE_ADMINISTRATOR ? $addButton : null;
?>
<div class="grid-row-open">
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['tax-code/view', 'id' => $model->id]);

        return ['data-url' => $url];
    },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'label' => 'Tax Name',
                'attribute' => 'tax_type_id',
                'value' => function ($data) {
                    return $data->taxType->name;
                },
            ],
            [
                'attribute' => 'province_id',
                'value' => function ($data) {
                    return $data->province->name;
                },
            ],
            'rate',
            'start_date:date',
        ],
    ]); ?>

</div>
