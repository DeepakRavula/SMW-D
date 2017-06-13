<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<div class="grid-row-open p-10">
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php $columns = [
		[
			'label' => 'Date',
			'value' => function ($data) {
				$date = Yii::$app->formatter->asDate($data->date);
				$lessonTime = (new \DateTime($data->date))->format('H:i:s');

				return !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
			},
		],
		[
			'label' => 'Status',
			'value' => function ($data) {
				$status = null;
				if (!empty($data->status)) {
					return $data->getStatus();
				}

				return $status;
			},
		],
                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{create} {view}',
                    'buttons' => [
                        'create' => function ($url, $model) {
                            $url = Url::to(['invoice/group-lesson', 'lessonId' => $model->id, 'enrolmentId' => null]);
                            if (!$model->hasGroupInvoice() && $model->isScheduled()) {
                                return Html::a('Create Invoice', $url, [
                                    'title' => Yii::t('yii', 'Create Invoice'),
                                                                'class' => ['btn-success btn-sm']
                                ]);
                            } else {
                                return null;
                            }
                        },
                        'view' => function ($url, $model) {
                            $url = Url::to(['lesson/view', 'id' => $model->id, '#' => 'student']);
                            if ($model->hasGroupInvoice() && $model->isScheduled()) {
                                return Html::a('View Invoice', $url, [
                                    'title' => Yii::t('yii', 'View Invoice'),
                                                                'class' => ['btn-info btn-sm']
                                ]);
                            } else {
                                return null;
                            }
                        }
                    ]
                ],
	];
     ?>   
    <?php echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
   		'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['lesson/view', 'id' => $model->id]);

        return ['data-url' => $url];
    },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => $columns,
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>

</div>