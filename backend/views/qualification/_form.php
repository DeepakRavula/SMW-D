<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Program;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Qualification;

/* @var $this yii\web\View */
/* @var $model common\models\Qualification */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-qualify">

<?php
if ($model->isNewRecord) {
    $url = Url::to(['qualification/create', 'id' => $teacherId, 'type' => $model->type]);
} else {
    $url = Url::to(['qualification/update', 'id' => $model->id]);
}
$form = ActiveForm::begin([
    'id' => 'modal-form',
    'action' => $url
]); ?>
    <?php $qualifications = Qualification::find()
        ->notDeleted()
        ->andWhere(['teacher_id' => $model->teacher_id])
        ->all();
        $teacherQualificationIds = ArrayHelper::getColumn($qualifications, 'program_id');
        $query = Program::find()->notDeleted()->active();
        if ($model->isNewRecord) {
            $query->andWhere(['NOT IN', 'program.id', $teacherQualificationIds]);
        }
    if ((int) $model->type === (int) Qualification::TYPE_HOURLY) {
        $query->privateProgram();
    } else {
        $query->group();
    }
    $programs = $query->all();
?>
   <div class="row">
	   
        <div class="col-md-8">
            <?php if ($model->isNewRecord) : ?>
                <?= $form->field($model, 'programs')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map($programs, 'id', 'name'),
                    'options' => [
                        'multiple' => true
                    ]
                ]); ?>
            <?php else: ?>
                <?= $form->field($model, 'program_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map($programs, 'id', 'name'),
                    'disabled' => !$model->isNewRecord
                ]); ?>
            <?php endif; ?>
        </div>
	   <?php if (Yii::$app->user->can('teacherQualificationRate')) : ?>
        <div class="col-md-4">
            <?= $form->field($model, 'rate')->textInput(['class' => 'right-align form-control']);?>
        </div>
	   <?php endif; ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>