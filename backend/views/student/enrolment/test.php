<?php

use yii\helpers\Url;
use common\models\User;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use common\models\Program;
use common\models\PaymentFrequency;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use common\models\LocationAvailability;
?>
<?php
$form = ActiveForm::begin([
    'id' => 'enrolment-form',
	'layout' => 'horizontal',
    ]);
?>
<?php
$privatePrograms = ArrayHelper::map(Program::find()
	->active()
	->andWhere(['type' => Program::TYPE_PRIVATE_PROGRAM])
	->all(), 'id', 'name')
?>
<?php
echo $form->field($model, 'programId')->widget(Select2::classname(), [
	'data' => $privatePrograms,
	'options' => ['placeholder' => 'Program']
]) ?>
<?php ActiveForm::end(); ?>
