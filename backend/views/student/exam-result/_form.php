<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
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
        ->andWhere(['raa.item_name' => 'teacher'])
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
    'id' => 'modal-form',
    'action' => $url,
]); ?>
<div class="row">
	<div class="col-md-6">
		<?php echo $form->field($model, 'date')->widget(DatePicker::className(), [
                'dateFormat' => 'php:M d, Y',
                'options' => [
                    'class' => 'form-control',
                    'readonly' => true,
                    'placeholder' => 'Select Date'
                ],
                'clientOptions' => [
                    'changeMonth' => true,
                    'yearRange' => '1500:3000',
                    'changeYear' => true,
                ],
            ]);?>
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
                'data' => ArrayHelper::map(Program::find()->notDeleted()->active()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
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
<?php ActiveForm::end(); ?>
</div>

<script>
    $(document).on('modal-success', function(event, params) {
        $.pjax.reload({container: "#student-exam-result-listing", replace: false, timeout: 4000});
        return false;
    });
    
    $(document).on('modal-delete', function(event, params) {
        $.pjax.reload({container: "#student-exam-result-listing", replace: false, timeout: 4000});
        return false;
    });
</script>