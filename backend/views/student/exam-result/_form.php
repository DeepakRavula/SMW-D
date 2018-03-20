<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Program;
use common\models\Location;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */
/* @var $form yii\bootstrap\ActiveForm */
?>
 <?php
 $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
 $teachers = ArrayHelper::map(
     User::find()
        ->joinWith(['userLocation ul' => function ($query) {
            $query->joinWith('teacherAvailability');
        }])
        ->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
        ->where(['raa.item_name' => 'teacher'])
        ->andWhere(['ul.location_id' => $locationId])
        ->notDeleted()
        ->all(),
    'id',
     'userProfile.fullName'
);
if ($model->isNewRecord) {
    $url = Url::to(['exam-result/create', 'studentId' => $model->studentId]);
} else {
    $url = Url::to(['exam-result/update', 'id' => $model->id]);
}
?>
<div class="lesson-form">
<?php $form = ActiveForm::begin([
    'id' => 'exam-result-form',
    'action' => $url,
]); ?>
<div class="row">
	<div class="col-md-6">
		<?=  $form->field($model, 'date')->widget(DatePicker::classname(), [
                'options' => [
                    'value' => !empty($model->date) ? Yii::$app->formatter->asDate($model->date) : Yii::$app->formatter->asDate(new \DateTime()),
           ],
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'M d,yyyy',
            ],
          ]);
        ?>
    </div>
	<div class="col-md-6">
		<?=  $form->field($model, 'mark')->textInput(['class' => 'right-align form-control']);?>
    </div>
	<div class="col-md-6">
		<?=  $form->field($model, 'level')->textInput();?>
    </div>
	<div class="col-md-6">
		<?=  $form->field($model, 'type')->textInput();?>
    </div>
	<div class="col-md-6">
		<?= $form->field($model, 'programId')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Program::find()->active()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                'options' => [
                                    'id' => 'examresult-programid'
                ],
                'pluginOptions' => [
                                    'multiple' => false,
                                    'placeholder' => 'Select Program',
                ],
            ]);
            ?>
    </div>
	<div class="col-md-6">
		<?php $teacher = !empty($model->teacherId) ? $model->teacher->publicIdentity : null; ?>
        <?php
            $teacherId = !empty($model->teacherId) ? $model->teacherId : null;
        echo $form->field($model, 'teacherId')->widget(
 
            DepDrop::classname(),
            [
            'data' => [$teacherId => $teacher],
            'type' => DepDrop::TYPE_SELECT2,
            'pluginOptions' => [
                'depends' => ['examresult-programid'],
                'placeholder' => 'Select...',
                'url' => Url::to(['course/teachers']),
            ],
        ]
 
        );
        ?>
        </div>
</div>
        <div class="clearfix"></div>
        <div class="row">
    <div class="col-md-12">
        <div class="pull-right">
		<?=  $form->field($model, 'id')->hiddenInput()->label(false);?>
        <?php echo Html::submitButton(Yii::t('backend', 'Cancel'), ['class' => 'btn btn-default exam-result-cancel-button', 'name' => 'signup-button']) ?>
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
        </div>
            <div class="pull-left">       
 <?php
                if (!$model->isNewRecord) {
                    echo Html::a('Delete', [
                'exam-result/delete', 'id' => $model->id
                ], [
                'id' => 'evaluation-delete-' . $model->id,
                'title' => Yii::t('yii', 'Delete'),
                'class' => 'evaluation-delete btn btn-danger',
            ]);
                }

        ?>
</div>
    </div>
    </div>
<?php ActiveForm::end(); ?>

</div>
