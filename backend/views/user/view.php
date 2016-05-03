<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\grid\EnumColumn;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->getPublicIdentity();
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <p>
        <?php echo Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
			[
				'label' => 'First Name',
				'value' => $model->userProfile->firstname, 
			],
			[
				'label' => 'Last Name',
				'value' => $model->userProfile->lastname, 
			],
            'email:email',
            'status',
			[
				'label' => 'Address',
				'value' => $model->primaryAddress->address, 
			],
			[
				'label' => 'Phone Number',
				'value' => $model->phoneNumber->number, 
			],
        ],
    ]) ?>

</div>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			[
				'label' => 'Name',
				'value' => function($data) {
					return $data->fullName;
                }, 	
			],
            'birth_date',
			[
				'label' => 'Customer Name',
				'value' => function($data) {
					$fullName = ! (empty($data->customer->userProfile->fullName)) ? $data->customer->userProfile->fullName : null;
					return $fullName;
                } 
			],
            ['class' => 'yii\grid\ActionColumn','controller' => 'student'],
        ],
    ]); ?>
