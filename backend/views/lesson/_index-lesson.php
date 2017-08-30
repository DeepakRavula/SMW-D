<?php

use yii\helpers\Url;
use common\models\Lesson;
use backend\models\search\LessonSearch;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="grid-row-open p-10">
<?php Pjax::begin(['id' => 'lesson-index','timeout' => 6000,]); ?>
    <?php $columns = [
            [
                'label' => 'Student',
				'attribute' => 'student',
                'value' => function ($data) {
                    return !empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
                },
            ],
            [
                'label' => 'Program',
				'attribute' => 'program',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
                },
            ],
            [
                'label' => 'Date',
				'attribute' => 'dateRange',
				'filter' => DateRangePicker::widget([
					'model' => $searchModel,
					'convertFormat' => true,
            		'initRangeExpr' => true,
					'attribute' => 'dateRange',
					'convertFormat' => true,
					'pluginOptions'=>[
						'autoApply' => true,
						'ranges' => [
							Yii::t('kvdrp', 'Today') => ["moment().startOf('day')", "moment()"],
							Yii::t('kvdrp', 'Tomorrow') => ["moment().startOf('day').add(1,'days')", "moment().endOf('day').add(1,'days')"],
							Yii::t('kvdrp', 'Next {n} Days', ['n' => 7]) => ["moment().startOf('day')", "moment().endOf('day').add(6, 'days')"],
							Yii::t('kvdrp', 'Next {n} Days', ['n' => 30]) => ["moment().startOf('day')", "moment().endOf('day').add(29, 'days')"],
						],
						'locale' => [
							'format' => 'M d,Y',
						],
						'opens' => 'right'
					],
				]),
                'value' => function ($data) {
                    $date = Yii::$app->formatter->asDate($data->date);
                    $lessonTime = (new \DateTime($data->date))->format('H:i:s');

                    return !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
                },
            ],
            [
                'label' => 'Status',
				'attribute' => 'lessonStatus',
				'filter' => LessonSearch::lessonStatuses(),
                'value' => function ($data) {
                    $status = null;
                    if (!empty($data->status)) {
                        return $data->getStatus();
                    }

                    return $status;
                },
            ],
            [
                'label' => 'Invoiced ?',
                'value' => function ($data) {
                    $status = null;
                    if (!empty($data->invoice)) {
                        $status = 'Yes';
                    } else {
                        $status = 'No';
                    }

                    return $status;
                },
            ],
        ];

        if ((int) $searchModel->type === Lesson::TYPE_GROUP_LESSON) {
            array_shift($columns);
        }
     ?>   
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'rowOptions' => function ($model, $key, $index, $grid) {
			$url = Url::to(['lesson/view', 'id' => $model->id]);

			return ['data-url' => $url];
		},
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => $columns,
    ]); ?>
	<?php Pjax::end(); ?>

</div>

