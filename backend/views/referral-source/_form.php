<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\ReleaseNotes */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="referral-sources-form">

  <?php $url = Url::to(['referral-source/update', 'id' => $model->id]);
    if ($model->isNewRecord) {
        $url = Url::to(['referral-source/create']);
    }
    $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => $url,
    ]);?>
        <?=$form->field($model, 'name')->textInput();?>
    <?php ActiveForm::end();?>

</div>

<script>
$(document).on('modal-success', function(event, params) {
        var url = "<?php echo Url::to(['referral-source/index']); ?>";
        $.pjax.reload({url: url, container: "#referral-sources-listing", replace: false, timeout: 4000});
        return false;
    });
</script>