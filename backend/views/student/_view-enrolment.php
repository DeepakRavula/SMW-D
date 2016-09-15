<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
?>

<?php yii\widgets\Pjax::begin(['id' => 'student-listing']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $enrolmentDataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
			[
				'label' => 'Program Name',
				'value' => function($data) {
					return ! (empty($data->course->program->name)) ? $data->course->program->name : null;
                } 
			],
			[
				'label' => 'Teacher Name',
				'value' => function($data) {
					return ! (empty($data->student->customer->publicIdentity)) ? $data->student->customer->publicIdentity : null;
                } 
			],
			[
				'label' => 'Start Date',
				'value' => function($data) {
					return ! (empty($data->course->startDate)) ? Yii::$app->formatter->asDate($data->course->endDate) : null;
                } 
			],
			[
				'label' => 'End Date',
				'value' => function($data) {
					return ! (empty($data->course->endDate)) ? Yii::$app->formatter->asDate($data->course->endDate) : null;
                } 
			],
        ],
    ]); ?>

	<?php yii\widgets\Pjax::end(); ?>