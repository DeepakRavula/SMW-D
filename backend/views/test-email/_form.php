<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\TestEmail;
/* @var $this yii\web\View */
/* @var $model common\models\EmailTemplate */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="test-mail-form">
<?php 
        $url = Url::to(['test-email/update', 'id' => $model->id]);
        $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => $url,
    ]); ?>
    	<?= $form->field($model, 'email')->textInput()?>
    <?php ActiveForm::end(); ?>
</div>
<script>
    $(document).on('modal-success', function(event, params) {
        var url = "<?php echo Url::to(['test-email/index']); ?>";
        $.pjax.reload({url: url, container: "#test-email-listing", replace: false, timeout: 4000});
        return false;
    });
</script>
