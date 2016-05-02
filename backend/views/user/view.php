<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

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
