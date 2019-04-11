<?php
use yii\helpers\Url;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

?>
    <?php
   $boxTools = "";
    ?>
    
    <?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => $boxTools,
        'title' => 'Payment Preference',
        'withBorder' => true,
    ])

    ?>
<?php if (!(empty($model->customerPaymentPreference))): ?>
      <dl class="dl-horizontal">
        <dt>Day Of Month</dt>
        <dd><?= $model->customerPaymentPreference->dayOfMonth ?></dd>
        <dt>Payment Method</dt>
        <dd><?= $model->customerPaymentPreference->getPaymentMethodName() ?></dd>
        <dt>Expiry Date</dt>
        <dd><?= Yii::$app->formatter->asDate($model->customerPaymentPreference->expiryDate); ?></dd>
    </dl>
<?php endif;?>
<?php LteBox::end() ?>

