<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\HolidaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Holidays';
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus" aria-hidden="true"></i>'), ['create'],['class' => 'btn btn-success pull-left']);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grid-row-open">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php yii\widgets\Pjax::begin(); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['holiday/view', 'id' => $model->id]);
        return ['data-url' => $url];
        },
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
        ],
    ]); ?>

	<?php yii\widgets\Pjax::end(); ?>

</div>
