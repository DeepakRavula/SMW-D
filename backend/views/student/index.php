<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Students';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-index">


    <p>
        <?php echo Html::a('Create Student', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'first_name',
            'last_name',
            'birth_date',
			[
				'label' => 'Customer Name',
				'value' => function($data) {
					return $data->customer->userProfile->fullName;
                }, 
			],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
