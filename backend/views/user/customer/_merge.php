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
            <?= $form->field($model, "customerIds")->dropDownList(
                    ArrayHelper::map(User::find()->notDeleted()->customers($locationId)->active()
                        ->all(), 'id', 'publicIdentity'), ['multiple' => 'multiple', 'size' => 9]); ?>
        </div>
        <div class="col-xs-2">
            <button type="button" id="undo_redo_undo" class="btn btn-primary btn-block">undo</button>
            <button type="button" id="groupcourse-title_rightAll" class="btn btn-default btn-block"><i class="glyphicon glyphicon-forward"></i></button>
            <button type="button" id="undo_redo_rightSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
            <button type="button" id="undo_redo_leftSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
            <button type="button" id="undo_redo_leftAll" class="btn btn-default btn-block"><i class="glyphicon glyphicon-backward"></i></button>
            <button type="button" id="undo_redo_redo" class="btn btn-warning btn-block">redo</button>
        </div>
        <div class="col-md-5">
            <select name="to[]" id="groupcourse-title_to" class="form-control" size="10" multiple="multiple"></select>
        </div>
        <div class="col-md-12 p-l-20 form-group">
            <?= Html::submitButton(Yii::t('backend', 'Merge'), ['class' => 'btn btn-info', 'name' => 'button']) ?>

            <?= Html::a('Cancel', '', ['class' => 'btn btn-default customer-merge-cancel']);?>
        </div>
    </div>
	<?php ActiveForm::end(); ?>
</div>
