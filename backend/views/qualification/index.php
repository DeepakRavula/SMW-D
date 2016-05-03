<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\QualificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qualifications';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qualification-index">

    <p>
        <?php echo Html::a('Create Qualification', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			[
				'label' => 'Teacher Name',
				'value' => function($data) {
					return $data->user->userProfile->fullName;
                }, 	
			],
			[
				'label' => 'Program Name',
				'value' => function($data) {
					return $data->program->name;
                }, 	
			],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
