<?php

use yii\grid\GridView;
use common\models\TimelineEventLink;
use yii\helpers\Html;

$this->title = Yii::t('backend', 'Timeline');
?>
<?php echo $this->render('_search', ['model' => $searchModel]); ?>
<?php $columns = [
		[
			'label' => 'Date',
			'value' => function ($data) {
				return Yii::$app->formatter->asDateTime($data->created_at);
			},
		],
		[
			'label' => 'Message',
			'format' => 'raw',
			'value' => function ($data) {
				$message = $data->message; 
				$regex = '/{{([^}]*)}}/';

				$replace = preg_replace_callback($regex, function($match)
				{
					$index = $match[1];
					$timelineEventLink = TimelineEventLink::findOne(['index' => $index]);
					$url = $timelineEventLink->baseUrl . $timelineEventLink->path; 
					$data[$index] = Html::a($index, $url);//'<a href=' . $url . '>' . $index . '</a>'; 
					return isset($data[$match[0]]) ? $data[$match[0]] : $data[$match[1]] ;
				}, $message);

				return $replace;
			},
		],
	];
 ?>   
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => $columns,
    ]); ?>

