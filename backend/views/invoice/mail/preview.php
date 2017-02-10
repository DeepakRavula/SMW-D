<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use common\models\InvoiceLineItem;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="student-form">
	<?php 
		$invoiceLineItems             = InvoiceLineItem::find()->where(['invoice_id' => $model->id]);
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoiceLineItems,
        ]);	
	$content = $this->render('content', [
		'toName' => !empty($model->user->publicIdentity) ? $model->user->publicIdentity : null,
		'model' => $model,
		'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
	]);?>
    <?php $form = ActiveForm::begin([
		'action' => Url::to(['invoice/send-mail', 'id' => $model->id])
	]); ?>
		<div class="row">
        <div class="col-lg-12">
			<?php $email = !empty($model->user->email) ?$model->user->email : null; ?>
            <?php echo $form->field($model, 'toEmailAddress')->textInput(['value' => $email]) ?>
        </div>
        </div>
		<div class="row">
        <div class="col-lg-12">
            <?php echo $form->field($model, 'subject')->textInput(['value' => 'Invoice from '.Yii::$app->name]) ?>
        </div>
        </div>
		<div class="row">
        <div class="col-lg-12">
            <?php echo $form->field($model, 'content')->widget(CKEditor::className(), [
        		'options' => [
					'value' => $content,
					'rows' => 6],
        		'preset' => 'basic',
    		]) ?>
        </div>
        </div>
    <div class="row-fluid">
    <div class="form-group col-lg-6">
       <?php echo Html::submitButton(Yii::t('backend', 'Send'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>
    <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
