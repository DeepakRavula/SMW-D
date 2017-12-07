<?php

use yii\helpers\Html;

?>

<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
            <?= Html::a('Cancel', '', ['class' => 'btn btn-default tax-adj-cancel']);?>    
           <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'adjust-tax-form-save btn btn-info', 'name' => 'signup-button']) ?>
        </div>
    </div>
</div>

