<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Student Birthdays';

?>
<div class="row">
    <div class="col-md-12">
        <h2 class="page-header">
            <span class="logo-lg"><b>Arcadia</b>SMW</span>
            <small class="pull-right"><?= Yii::$app->formatter->asDate('now'); ?></small>
        </h2>
    </div>
</div>
<div class="row">
    <div class="col-md-6 invoice-col">
        <div class="invoice-print-address">
            <address>
                <strong> Arcadia Music Academy ( <?= $model->name; ?> )</strong><br/>
                <?php if (!empty($model->address)): ?>
                    <?= $model->address ?><br/>
                <?php endif; ?>
                <?php if (!empty($model->city_id)): ?>
                    <?= $model->city->name ?>
                <?php endif; ?>
                <?php if (!empty($model->province_id)): ?>
                    <?= ', ' . $model->province->name ?><br/>
                <?php endif; ?>
                <?php if (!empty($model->postal_code)): ?>
                    <?= $model->postal_code ?><br/>
                <?php endif; ?>    
                <?php if (!empty($model->phone_number)): ?>
                    <?= $model->phone_number ?>
                <?php endif; ?>
                <br/>
                www.arcadiamusicacademy.com
            </address>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<div>
    <h3><strong>Student Birthday Report From  <?= $searchModel->fromDate->format('F jS') . ' to ' . $searchModel->toDate->format('F jS'); ?></strong></h3></div>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
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
				'label' => 'First Name',
				'value' => 'first_name', 
			],
            [
				'label' => 'Last Name',
				'value' => 'last_name', 
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