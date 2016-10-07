<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ProfessionalDevelopmentDaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Professional Development Days';
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add'), ['create'], ['class' => 'btn btn-primary btn-sm']);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="professional-development-day-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
   <?php yii\widgets\Pjax::begin(); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
			[
                'attribute' => 'date',
				'label' => 'Date',
				'value' => function($data) {
					return ! (empty($data->date)) ? Yii::$app->formatter->asDate($data->date) : null;
                } 
			],
			['class' => 'yii\grid\ActionColumn','template' => '{view}'],
        ],
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>
</div>
