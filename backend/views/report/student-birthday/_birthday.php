<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Student Birthdays';

?>
<style>
@media print{
	.print-container{
		margin-top:5px;
	}
	.text-left{
        text-align: left !important;
    }
	.location-address {
	  text-align : right;
	  font-size : 18px;
	}
	.location-address p{
		margin:0;
		padding:0;
		font-weight:normal;
	}
	.boxed {
	  border: 4px solid #949599;
	  height: 200px;
	  margin: 20px;
	  padding: 20px;
	  width: 580px;
	}
	.sign {
	  font-weight: bold;
	  text-align : right;
	  font-size : 28px;
	}
	.sign span {
	  width: 250px;
	  display: inline-block;
	  border-bottom: 1px solid #999999;
	  font-weight: normal;
	}
	.login-logo-img {
		width:300px !important;
		height:auto;
	}
	.report-grid #student-birthday-grid table thead{
		border-bottom: 1px ridge;
	}
	.report-grid #student-birthday-grid table tbody tr.kv-grid-group-row{
		border-bottom: 1px ridge;
	}
	.report-grid #student-birthday-grid table tbody tr.kv-group-footer{
		border-top: 1px ridge;
	}
	.report-grid .table-bordered{
		border: 1px solid transparent;
	}
	.report-grid .table-bordered>thead>tr>th, .report-grid .table-bordered>tbody>tr>th,.report-grid  .table-bordered>tfoot>tr>th,.report-grid  .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .report-grid .table-bordered>tfoot>tr>td{
		border:none !important;
	}
	.report-grid .table-bordered > tbody > tr:nth-child(even){
		
	}
}
@page{
  size: auto;
  margin: 3mm;
}
</style>
<div class="row-fluid print-container">
	<div class="logo invoice-col">              
		<img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
	</div>
	<div class="location-address">
			<p>Arcadia Music Academy ( <?= $model->name;?> )</p>
			<p><?php if (!empty($model->address)): ?>
				<?= $model->address ?><br>
			<?php endif; ?></p>
			<p><?php if (!empty($model->city_id)): ?>
				<?= $model->city->name ?>
			<?php endif; ?>
			<?php if (!empty($model->province_id)): ?>
				<?= ', ' . $model->province->name ?>
			<?php endif; ?> </p>
	</div>
	<div class="clearfix"></div>
</div>
<div>
<?php $reportText = 'Student Birthdays'; ?>
<h3><strong><?= $reportText; ?> Report </strong></h3></div>
<div><h3><?= $searchModel->fromDate->format('F jS') . ' to ' . $searchModel->toDate->format('F jS');?></h3></div>
<div class="report-grid">
<?php yii\widgets\Pjax::begin(['id' => 'birthday-listing']); ?>
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

<?php yii\widgets\Pjax::end(); ?>
</div>