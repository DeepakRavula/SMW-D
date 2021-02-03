<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\color\ColorInput;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Classroom;
use common\models\Location;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-qualify">
<?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validationUrl' => Url::to(['lesson/validate-on-update', 'id' => $model->id, 'teacherId' => null]),
        'action' => Url::to(['lesson/edit-classroom', 'id' => $model->id]),
        'options' => [
            'class' => 'p-10',
        ]
    ]); ?>
	   <div class=" col-md-5">
		   <?php $locationId = Location::findOne(['slug' => \Yii::$app->location])->id; ?>
		   <?=
           $form->field($model, 'classroomId')->widget(Select2::classname(), [
               'data' => ArrayHelper::map(Classroom::find()->notDeleted()->orderBy(['name' => SORT_ASC])
                       ->andWhere(['locationId' => $locationId])->all(), 'id', 'name'),
               'pluginOptions' => [
                   'placeholder' => 'Select Classroom',
               ]
           ]);
           ?>
		</div>
        <div class="col-md-5">
        <?php echo $form->field($model, 'colorCode')->widget(ColorInput::classname(), [
                'options' => [
                    'placeholder' => 'Select color ...',
                    'value' => $model->getColorCode(),
                ],
        ]);
        ?>
       
        </div>
        <div class="col-md-5">
       <?php $model->isOnline = $model->is_online ? 1: 0; ?>
       <?= $form->field($model, 'is_online')->checkbox();
        ?>
        </div>
    <div class="clearfix"></div>
	<?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function () {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Details</h4>');
        $('#popup-modal .modal-dialog').css({'width': '500px'});
    });
</script>