<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\HolidaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Holidays';
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus" aria-hidden="true"></i>'), ['create'],['class' => 'btn btn-success pull-left']);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="holiday-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php yii\widgets\Pjax::begin(); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
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
