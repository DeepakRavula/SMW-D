<?php

use common\models\User;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div id="warning-notification" style="display:none;" class="alert-warning alert fade in"></div>

<?php $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id; ?>
<div>
    
    <?php $form = ActiveForm::begin([
            'id' => 'customer-merge-form',
    ]); ?>
    <div class="row">
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="pull-right">
            <?= Html::a('Cancel', '', ['class' => 'btn btn-default customer-merge-cancel']);?>
            <?= Html::submitButton(Yii::t('backend', 'Merge'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
        </div>
    </div>
	<?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function(){
        $('#user-customerid').multiselect({
            search: {
                left: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
            }
        });
    });
</script>
