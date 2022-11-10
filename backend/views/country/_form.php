<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Country */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="country-form">

    <?php   $url = Url::to(['country/update', 'id' => $model->id]);
	    if ($model->isNewRecord) {
		$url = Url::to(['country/create']);
	    }
	$form = ActiveForm::begin([
	'id' => 'modal-form',
	'action' => $url,
    ]); ?>

    <div class="row">
		<div class="col-md-8">
<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	    </div>
    </div>
<?php ActiveForm::end(); ?>

</div>
<script>
    $(document).on('modal-success', function(event, params) {
	var url = "<?php echo Url::to(['country/index']); ?>";
	$.pjax.reload({url: url, container: "#country-listing", replace: false, timeout: 4000});
	return false;
    });
    $(document).on('modal-delete', function(event, params) {
	var url = "<?php echo Url::to(['country/index']); ?>";
+        $.pjax.reload({url: url, container: "#country-listing", replace: false, timeout: 4000});
	return false;
    });
</script>