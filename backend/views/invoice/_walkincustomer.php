<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php $form = ActiveForm::begin([
        'method' => 'post',
        'id' => 'walkin-customer-form',
		'action' => Url::to(['invoice/create-walkin', 'id' => $model->id])
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($userModel, 'firstname'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($userModel, 'lastname'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($userEmail, 'email'); ?>
        </div>
      </div>
    <div class="row">  
        <div class="col-md-12">
            <div class="pull-right">
             <?= Html::a('Cancel', '#', ['class' => 'btn btn-default invoice-walkin-customer-update-cancel-button']);?>    
            <?php echo Html::submitButton('Save', ['class' => 'btn btn-info', 'name' => 'guest-invoice']) ?>
            </div>
            </div>
    </div>
</div>

<?php ActiveForm::end(); ?>