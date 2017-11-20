<?php

use kartik\date\DatePicker;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'New Enrolment';
?>
<div class="row user-create-form">
			<div class="col-md-5">
				<?php
            echo $form->field($model, 'first_name')->textInput(['placeholder' => 'First Name']); ?>
			</div>
			<div class="col-md-5">
				<?php
            echo $form->field($model, 'last_name')->textInput(['placeholder' => 'Last Name']); ?>
			</div>
		<div class="col-md-5">
		<?php echo $form->field($model, 'birth_date')->widget(DatePicker::classname(), [
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy',
                ],
            ]);
            ?>
        </div>
	<div class="clearfix"></div>
	<div class="pull-right">
		<?= Html::a('Cancel', '#', ['class' => 'm-r-10 btn btn-default new-enrol-cancel']); ?>
		<button class="nextBtn btn btn-info pull-right" type="button" >Save</button>
	</div>
</div> <!-- ./container -->