<?php

use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;
use yii\helpers\Url;
use common\models\Student;
use common\components\gridView\KartikGridView;
use yii\helpers\ArrayHelper;
use common\models\Location;
use common\models\UserProfile;
use common\models\User;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php $this->registerCssFile("@web/css/student/style.css");?>
<?php $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;?>
<?php yii\widgets\Pjax::begin([
    'enablePushState' => false,
    'timeout' => 6000,
	'id' => 'student-listing']); ?>
<?php
echo KartikGridView::widget([
    'id' => 'student-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
        'summary' => false,
        'emptyText' => false,
    'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel) {
        $url = Url::to(['student/view', 'id' => $model->id]);
        $data = ['data-url' => $url];
        if ($searchModel->showAllStudents) {
            if ($model->status === Student::STATUS_INACTIVE) {
                $data = array_merge($data, ['class' => 'danger inactive']);
            } elseif ($model->status === Student::STATUS_ACTIVE) {
                $data = array_merge($data, ['class' => 'info active']);
            }
        }

        return $data;
    },
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
        [
            'label' => 'First Name',
            'attribute' => 'first_name',
            'value' => function ($data) {
                return !(empty($data->first_name)) ? $data->first_name : null;
            },
            'filterType'=>KartikGridView::FILTER_SELECT2,
                'filter'=>ArrayHelper::map(Student::find()->orderBy(['first_name' => SORT_ASC])
                ->joinWith(['enrolment' => function ($query) {
                    $query->joinWith(['course' => function ($query) {
                        $query->confirmed()
                                ->location(Location::findOne(['slug' => \Yii::$app->location])->id);
                    }]);
                }])
                ->asArray()->all(), 'id', 'first_name'),
                'filterWidgetOptions'=>[
            'options' => [
                'id' => 'first-name',
            ],
                    'pluginOptions'=>[
                        'allowClear'=>true,
            ],
        ],
                'filterInputOptions'=>['placeholder'=>'Student'],
            ],
        [
            'label' => 'Last Name',
            'attribute' => 'last_name',
            'value' => function ($data) {
                return !(empty($data->last_name)) ? $data->last_name : null;
            },
            'filterType'=>KartikGridView::FILTER_SELECT2,
            'filter'=>ArrayHelper::map(Student::find()->orderBy(['last_name' => SORT_ASC])
            ->joinWith(['enrolment' => function ($query) {
                $query->joinWith(['course' => function ($query) {
                    $query->confirmed()
                            ->location(Location::findOne(['slug' => \Yii::$app->location])->id);
                }]);
            }])
            ->asArray()->all(), 'id', 'last_name'),
            'filterWidgetOptions'=>[
        'options' => [
            'id' => 'last-name',
        ],
                'pluginOptions'=>[
                    'allowClear'=>true,
        ],
    ],
            'filterInputOptions'=>['placeholder'=>'Student'],
        ],
            [
            'label' => 'Customer',
	    'attribute' => 'customer',
            'value' => function ($data) {
                $fullName = !(empty($data->customerProfile->fullName)) ? $data->customerProfile->fullName : null;

                return $fullName;
            },
            'filterType'=> KartikGridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(User::find()
			    ->customers($locationId)
			    ->joinWith(['userProfile' => function ($query) {
					$query->orderBy('firstname');
				}])
			    ->all(), 'id', 'publicIdentity'),
	    'filterWidgetOptions'=>[
        'options' => [
            'id' => 'customer',
        ],
                'pluginOptions'=>[
                    'allowClear'=>true,
        ],

    ],
            'filterInputOptions'=>['placeholder'=>'Customer'],
            'format'=>'raw'
],
            [
            'label' => 'Phone',
	    'attribute' => 'phone',
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($data) {
                return $data->customer->getPhone();
            },
        ],
    ],
]);
?>
<?php yii\widgets\Pjax::end(); ?>
