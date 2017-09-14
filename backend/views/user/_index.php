<?php
use common\components\gridView\AdminLteGridView;
use yii\helpers\ArrayHelper;
use common\models\Invoice;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */ 

$roles = ArrayHelper::getColumn(Yii::$app->authManager->getRoles(), 'description');
foreach ($roles as $name => $description) {
    if ($name === $searchModel->role_name) {
        $role = $description;
        break;
    }
}
$roleName = $searchModel->role_name;
$originalInvoice = Invoice::TYPE_INVOICE;?>
<div class="grid-row-open">
<?= AdminLteGridView::widget([
            'dataProvider' => $dataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel, $roleName, $originalInvoice) {
                $url = Url::to(['user/view', 'UserSearch[role_name]' => $roleName, 'id' => $model->id]);
            	$data = ['data-url' => $url];
				if ($searchModel->showAllCustomers || $searchModel->showAllTeachers) {
					if ((int)$model->status === User::STATUS_NOT_ACTIVE) {
						$data = array_merge($data, ['class' => 'danger inactive']);
					} elseif ((int)$model->status === User::STATUS_ACTIVE) {
						$data = array_merge($data, ['class' => 'info active']);
					}
            }

            return $data;
            },
            'tableOptions' => ['class' => 'table table-bordered'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
			'filterModel' => $searchModel,
            'columns' => [
            [
                'attribute' => 'firstname',
                'label' => 'First Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->firstname) ? $data->userProfile->firstname : null;
                },
            ],
            [
                'attribute' => 'lastname',
                'label' => 'Last Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->lastname) ? $data->userProfile->lastname : null;
                },
            ],
            'email',
            [
                'label' => 'Phone',
                'value' => function ($data) {
                    return !empty($data->phoneNumber->number) ? $data->phoneNumber->number : null;
                },
            ],
        ],
    ]); ?>
</div>