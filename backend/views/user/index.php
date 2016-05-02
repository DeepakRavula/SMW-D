<?php

use common\grid\EnumColumn;
use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a(Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'User',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
			[
				'label' => 'User Name',
				'value' => function($data) {
					return $data->userProfile->fullName;
                },
			],
            'email:email',
            [
                'class' => EnumColumn::className(),
                'attribute' => 'status',
                'enum' => User::statuses(),
                'filter' => User::statuses()
            ],
			[
				'label' => 'Primary Address',
				'value' => function($data) {
					$Address = ! (empty($data->primaryAddress->address)) ? $data->primaryAddress->address : null;
					return $Address;
                },
			],
			[
				'label' => 'Phone',
				'value' => function($data) {
					return $data->phoneNumber->number;
                },
			],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
