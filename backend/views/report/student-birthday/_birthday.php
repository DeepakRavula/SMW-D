<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use Carbon\Carbon;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Student Birthdays';

?>
<?php
   echo $this->render('/print/_header', [
       'locationModel'=>$model,
]);
   ?>
<div>
    <h3><strong>Student Birthday Report From  <?= Carbon::parse($searchModel->fromDate)->format('M d') . ' to ' . Carbon::parse($searchModel->toDate)->format('M d'); ?></strong></h3></div>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['student/view', 'id' => $model->id]);
            $data = ['data-url' => $url];
            return $data;
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
            'pjax' => true,
            'pjaxSettings' => [
        'neverTimeout' => true,
        'options' => [
            'id' => 'student-birthday-grid',
        ],
                ],
        'columns' => [
            [
                'label' => 'Name',
                'value' => function ($data) {
                    return $data->fullName;
                },
            ],
            [
                'label' => 'Birth Date',
                'value' => 'birth_date',
            ],
            [
                'label' => 'Customer',
                'value' => 'customer.userProfile.fullName',
            ],
            [
                'label' => 'Phone',
                'value' => 'customer.phoneNumber.number',
            ],
            [
                'label'=>'Email',
                'value'=> 'customer.email',
                'contentOptions' => ['class' => 'text-left'],
                'headerOptions' => ['class' => 'text-left'],
            ]
            ]
    ]);

    ?>
</div>