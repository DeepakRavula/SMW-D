<?php

use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\UserEmail;
use common\models\Invoice;
use dosamigos\ckeditor\CKEditor;
/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="student-form">
	<?php
        $invoiceModel = Invoice::findOne($id);
        $model->content = $this->renderAjax('/invoice/mail/content', [
            'model' => $invoiceModel,
            'searchModel' => $searchModel,
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'emailTemplate' => $emailTemplate,
            'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
        ]);
    $model->to = $emails;
        $data=null;
        if (!empty($userModel)) {
            $data = ArrayHelper::map(UserEmail::find()->joinWith('userContact')->andWhere(['user_contact.userId'=>$userModel->id])->orderBy('user_email.email')->all(), 'email', 'email');
        }
        ?>
	<?php $form = ActiveForm::begin([
        'id' => 'mail-form',
        'action' => Url::to(['email/send'])
    ]);
    ?>
	<?= $form->field($model, 'id')->hiddenInput(['value' => $id])->label(false);?>
	<div class="row">
        <div class="col-lg-12">
			<?php
            echo $form->field($model, 'to')->widget(Select2::classname(), [
                'data' => $data,
                'pluginOptions' => [
                    'tags' => true,
                    'multiple' => true,
                ],
            ]);
            ?>
        </div>
	</div>
	<div class="row">
        <div class="col-lg-12">
            <?php
            echo $form->field($model, 'subject')->textInput(['value' => $subject]) ?>
        </div>
	</div>
	<div class="row">
        <div class="col-lg-12">
			<?php
            echo $form->field($model, 'content')->widget(CKEditor::className());
            ?>

        </div>
	</div>
<?php ActiveForm::end(); ?>
</div>