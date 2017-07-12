<?php

use common\models\User;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>

<?php $locationId = \Yii::$app->session->get('location_id'); ?>
<div>
    
    <?php $form = ActiveForm::begin([
            'id' => 'customer-merge-form',
    ]); ?>
    <div class="row">
        <div class="col-md-5">
            <?= $form->field($model, 'customerIds')->dropDownList([], ['multiple' => 'multiple',
                    'id' => 'user-customerid_to', 'size' => '10']); ?>
        </div>
        <div class="col-xs-2">
            <button type="button" id="user-customerid_undo" class="btn btn-primary btn-block">undo</button>
            <button type="button" id="user-customerid_leftSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
            <button type="button" id="user-customerid_rightSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
            <button type="button" id="user-customerid_redo" class="btn btn-warning btn-block">redo</button>
        </div>
        <div class="col-md-5">
            <?= $form->field($model, "customerId")->dropDownList(
                    ArrayHelper::map(User::find()->customers($locationId)->notDeleted()->active()
                        ->all(), 'id', 'publicIdentity'), ['multiple' => 'multiple', 'size' => '10']); ?>

        </div>
        <div class="col-md-12 p-l-20 form-group">
            <?= Html::submitButton(Yii::t('backend', 'Merge'), ['class' => 'btn btn-info', 'name' => 'button']) ?>

            <?= Html::a('Cancel', '', ['class' => 'btn btn-default customer-merge-cancel']);?>
        </div>
    </div>
	<?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function(){
        $('#user-customerid').multiselect({
            search: {
                left: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
                right: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
            }
        });
    });
</script>
