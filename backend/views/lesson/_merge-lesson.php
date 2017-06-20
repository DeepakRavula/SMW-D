<?php
use common\models\LessonSplit;
use yii\data\ActiveDataProvider;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<?php
$courseId = $model->courseId;
$locationId = $model->course->locationId;
$lessons = LessonSplit::find()
        ->unusedSplits($courseId, $locationId);
$splitLessonDataProvider = new ActiveDataProvider([
	'query' => $lessons,
	'pagination' => false
]);
?>
<div>
    <?php
    Modal::begin([
        'header' => '<h4 class="m-0">Merge Lesson</h4>',
        'id'=>'merge-lesson-modal',
    ]);?>
    <h5><strong><?= 'Please choose the lesson that should be merged.'; ?></strong></h5>
	<?php $form = ActiveForm::begin([
		'id' => 'merge-lesson-form',
	]); ?>
	<div>
	<?php
    echo GridView::widget([
        'dataProvider' => $splitLessonDataProvider,
        'options' => ['class' => 'col-md-12'],
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'class' => 'yii\grid\RadioButtonColumn',
                'radioOptions' => function ($model) {
                    return [
                        'value' => $model['id'],
                        'checked' => $model['id'] == 2
                    ];
                }
            ],
            [
                'label' => 'Program',
                'value' => function ($data) {
                    return !empty($data->lesson->course->program->name) ? $data->lesson->course->program->name : null;
                },
            ],
            [
                'label' => 'Invoice Status',
                'value' => function ($data) {
                    $status = null;
                    if (!empty($data->lesson->invoice)) {
                        return $data->lesson->invoice->getStatus();
                    } else {
                        $status = 'Not Invoiced';
                    }

                    return $status;
                },
            ],
            [
                'label' => 'Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->lesson->date).' @ '.Yii::$app->formatter->asTime($data->lesson->date);
                },
            ],
            [
                'label' => 'Prepaid?',
                'value' => function ($data) {
                    if (!empty($data->lesson->proFormaInvoice) && ($data->lesson->proFormaInvoice->isPaid() || $data->lesson->proFormaInvoice->hasCredit())) {
                        return 'Yes';
                    }

                    return 'No';
                },
            ],
        ],
    ]);
	?>
	</div>
	<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
	<?php ActiveForm::end(); ?>
<?php Modal::end(); ?>
</div>
