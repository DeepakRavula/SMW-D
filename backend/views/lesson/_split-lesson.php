<?php
use common\models\Lesson;
use yii\data\ActiveDataProvider;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
?>
<?php
$locationId = Yii::$app->session->get('location_id');
$lessons = Lesson::find()
	->location($locationId)
	->andWhere(['>', 'DATE(date)', (new \DateTime($model->date))->format('Y-m-d')])
	->andWhere(['courseId' => $model->courseId])
	->scheduled()
    ->notDeleted();

$lessonDataProvider = new ActiveDataProvider([
	'query' => $lessons,
	'pagination' => false
]);
?>
<div>
 <?php
 Modal::begin([
    'header' => '<h4 class="m-0">Split Lesson</h4>',
    'id'=>'split-lesson-modal',
]);?>
	<?php $form = ActiveForm::begin([
		'id' => 'split-lesson-form',
		'action' => Url::to(['lesson/split', 'id' => $model->id])
	]); ?>
	<?php
    echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'options' => ['class' => 'col-md-12'],
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
			[
                'class' => 'yii\grid\CheckboxColumn',
				'name' => 'splitLessonIds'
            ],
            [
                'label' => 'Program',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
                },
            ],
            [
                'label' => 'Status',
                'value' => function ($data) {
                    $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
                    $currentDate = new \DateTime();

                    if ($lessonDate <= $currentDate) {
                        $status = 'Completed';
                    } else {
                        $status = 'Scheduled';
                    }

                    return $status;
                },
            ],
            [
                'label' => 'Invoice Status',
                'value' => function ($data) {
                    $status = null;
                    if (!empty($data->invoice)) {
                        return $data->invoice->getStatus();
                    } else {
                        $status = 'Not Invoiced';
                    }

                    return $status;
                },
            ],
            [
                'label' => 'Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date).' @ '.Yii::$app->formatter->asTime($data->date);
                },
            ],
            [
                'label' => 'Prepaid?',
                'value' => function ($data) {
                    if (!empty($data->proFormaInvoice) && ($data->proFormaInvoice->isPaid() || $data->proFormaInvoice->hasCredit())) {
                        return 'Yes';
                    }

                    return 'No';
                },
            ],
        ],
    ]);
	?>
	<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
	<?php ActiveForm::end(); ?>
<?php Modal::end(); ?>
</div>
