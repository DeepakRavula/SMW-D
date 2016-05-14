<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-index">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			[
				'label' => 'Student Name',
				'value' => function($data) {
					return ! empty($data->studentName->fullName) ? $data->studentName->fullName : null;
                }, 	
			],
			[
				'label' => 'Program Name',
				'value' => function($data) {
					return ! empty($data->programName->name) ? $data->programName->name : null;
                }, 	
			],
            'quantity',
            // 'commencement_date',
            // 'invoiced_id',
            // 'location_id',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <p>
        <?php echo Html::a('Create Lesson', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
</div>
