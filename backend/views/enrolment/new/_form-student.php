<?php

use yii\jui\DatePicker;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */

?>
<div class="row user-create-form">
	<div class="col-xs-5">
        <label class="modal-customer-label">First Name</label>
    </div>
			<div class="col-xs-7">
				<?php
            echo $form->field($model, 'first_name')->textInput(['placeholder' => 'First Name'])->label(false); ?>
			</div>
	<div class="col-xs-5">
        <label class="modal-customer-label">Last Name</label>
    </div>
			<div class="col-xs-7">
				<?php
            echo $form->field($model, 'last_name')->textInput(['placeholder' => 'Last Name'])->label(false); ?>
			</div>
	<div class="col-xs-5">
        <label class="modal-customer-label">Birth Date</label>
    </div>
	<div class="col-xs-7">
	   <?php echo $form->field($model, 'birth_date')->widget(DatePicker::className(), [
                'dateFormat' => 'php:M d, Y',
                'clientOptions' => [
                    'changeMonth' => true,
                    'yearRange' => '1500:3000',
                    'changeYear' => true,
                ],
            ])->textInput(['placeholder' => 'Select Date'])->label(false);?>
        </div>
	<div class="clearfix"></div>
	<div class="pull-right">
		<?= Html::a('Cancel', '#', ['class' => 'm-r-10 btn btn-default new-enrol-cancel']); ?>
		 <?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'), ['class' => 'btn btn-info', 'name' => 'signup-button', 'id' => 'new-enrol-save-btn']) ?>
	</div>
	<div class="form-group">
		<button class=" modal-form-label step4-back btn btn-info" type="button" >Back</button>
    </div>
</div> <!-- ./container -->