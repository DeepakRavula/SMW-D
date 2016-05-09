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
			[
				'label' => 'Name',
				'value' => function($data) {
					return ! empty($data->fullName) ? $data->fullName : null;
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
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

	<p>
        <?php echo Html::a('Create Student', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

</div>
