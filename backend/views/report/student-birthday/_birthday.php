<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Student Birthdays';

?>
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
    <h3><strong>Student Birthday Report For  <?= $searchModel->fromDate->format('F jS') . ' to ' . $searchModel->toDate->format('F jS');?></strong></h3></div>
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