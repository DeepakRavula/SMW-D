<?php
use common\models\Lesson;
use yii\data\ActiveDataProvider;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php
$studentId = $model->student->id;
$locationId = $model->course->locationId;
$lessons = Lesson::find()
        ->split()
        ->notCanceled()
        ->notDeleted()
        ->unscheduled()
        ->student($studentId);
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
    <div id="merge-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
    <h5><strong><?= 'Please choose the lesson that should be merged.'; ?></strong></h5>
	<?php $form = ActiveForm::begin([
        'id' => 'merge-lesson-form',
    ]); ?>
	<div>
	<?php
    echo GridView::widget([
        'dataProvider' => $splitLessonDataProvider,
        'options' => ['class' => 'col-md-12'],
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'class' => 'yii\grid\RadioButtonColumn',
                'radioOptions' => function ($model) {
                    return [
                        'value' => $model['id'],
                        'checked' => true
                    ];
                }
            ],
            [
                'label' => 'Program',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
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
	</div>
	<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
	<?php ActiveForm::end(); ?>
<?php Modal::end(); ?>
</div>
