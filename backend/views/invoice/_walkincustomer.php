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
		'action' => Url::to(['invoice/update-walkin', 'id' => $model->id])
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($userModel, 'firstname'); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($userModel, 'lastname'); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($userEmail, 'email'); ?>
        </div>
        
        <div class="col-md-12">
            <div class="pull-right">
             <?= Html::a('Cancel', '#', ['class' => 'btn btn-default invoice-walkin-customer-update-cancel-button']);?>    
            <?php echo Html::submitButton('Add ', ['class' => 'btn btn-info m-r-10', 'name' => 'guest-invoice']) ?>
            </div>
            </div>
    </div>
</div>

<?php ActiveForm::end(); ?>