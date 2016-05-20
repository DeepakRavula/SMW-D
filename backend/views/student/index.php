<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Students';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-index">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			'first_name',
            'last_name',
            'birth_date:date',
			[
				'label' => 'Customer Name',
				'value' => function($data) {
					$fullName = ! (empty($data->customer->userProfile->fullName)) ? $data->customer->userProfile->fullName : null;
					return $fullName;
                } 
			],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

	

</div>
