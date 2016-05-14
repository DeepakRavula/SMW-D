<?php

use common\grid\EnumColumn;
use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend',  ! ($searchModel->role_name) ? ucwords($searchModel->role_name) : 'User');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
			[
				'label' => 'User Name',
				'value' => function($data) {
					return ! empty($data->userProfile->fullName) ? $data->userProfile->fullName : null;
                },
			],
            'email:email',
			[
				'label' => 'Phone',
				'value' => function($data) {
					return ! empty($data->phoneNumber->number) ? $data->phoneNumber->number : null;
                },
			],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
	<p>
        <?php echo Html::a(Yii::t('backend', 'Create ' .  (! empty($searchModel->role_name) ? ucwords($searchModel->role_name) : 'User'), [
    'modelClass' => 'User',
]), ['create', 'User[role_name]' => $searchModel->role_name], ['class' => 'btn btn-success']) ?>
    </p>

	<?php if($searchModel->role_name === User::ROLE_CUSTOMER):?>
    <p>
        <?php echo Html::a(Yii::t('backend', 'Delete All Customers', [
    'modelClass' => 'User',
]), ['delete-all'], ['class' => 'btn btn-danger']) ?>
    </p>
	<?php endif;?>
</div>
